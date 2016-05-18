<?php

namespace Bozboz\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Bozboz\Admin\Media\Media;
use Bozboz\Ecommerce\Products\Categories\Category;
use Bozboz\Ecommerce\Products\ProductInterface;
use Bozboz\Ecommerce\Products\Feature;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class ShopController extends Controller
{
	private $product;
	private $category;

	public function __construct(ProductInterface $product, Category $category)
	{
		$this->product = $product;
		$this->category = $category;
	}

	public function index()
	{
		return $this->productListing($this->product->visible()/*Feature::first()->products()->visible()->getQuery()*/);
	}

	public function search()
	{
		$query = Input::get('q');
		$results = $this->product->visible()->search($query);

		return $this->productListing($results)->with([
			'heading' => sprintf('Search results for "%s"', $query),
			'title' => sprintf('Search results for "%s"', $query)
		]);
	}

	public function filter($filter)
	{
		$products = $this->product->visible()->whereHas('manufacturer', function($q) use ($filter) {
			$q->where('slug', $filter);
		});

		return $this->productListing($products)->with([
			'heading' => sprintf('Filter by "%s"', $filter),
			'title' => sprintf('Filter by "%s"', $filter),
			'filter' => $filter
		]);
	}

	public function productWithCategory($categorySlug, $productSlug)
	{
		$product = $this->product->where('slug', $productSlug)->firstOrFail();
		$category = $product->categories()->whereSlug($categorySlug)->firstOrFail();

		$product->category = $category;
		return $this->productDetail($product);
	}

	public function productOrCategory($slug)
	{
		if ($product = $this->product->where('slug', $slug)->first()) {
			return $this->productDetail($product)->with([
				'category' => $product->category
			]);
		}

		if ($category = $this->category->where('slug', $slug)->first()) {
			return $this->productListing($category->products()->visible()->getQuery())->with([
				'heading' => $category->name,
				'title' => $category->meta_title,
				'category' => $category
			]);
		}

		throw new ModelNotFoundException;
	}

	protected function productListing(Builder $builder)
	{
		$this->filterPrice($builder, Input::get('price'));
		$this->sortOrder($builder, Input::get('sort'));

		return view('ecommerce::products.listing')->with([
			'items' => $builder->paginate(12),
			'detailRoute' => $this->getDetailRoute()
		]);
	}

	protected function productDetail(ProductInterface $product)
	{
		if ($product->variants->count()) {
			$media = Media::forCollection($product->variants)->get();
		} else {
			$media = Media::forModel($product)->get();
		}

		return view('ecommerce::products.product')->with([
			'product' => $product,
			'available' => $product->isAvailable(),
			'media' => $media,
			'title' => $product->meta_title
		]);
	}

	protected function getDetailRoute()
	{
		return 'products.detail';
	}

	protected function filterPrice(Builder $products, $price)
	{
		$products->byPrice($price);
	}

	protected function sortOrder(Builder $products, $sortOrder)
	{
		switch($sortOrder) {
			case 'name':
				return $products->orderBy('name');
			case 'expensive':
				return $products->orderBy('price', 'desc');
			case 'cheapest':
				return $products->orderBy('price', 'asc');
			case 'newest':
				return $products->latest('products.created_at');
			case 'oldest':
				return $products->oldest('products.created_at');
			default:
				return $products;
		}
	}
}
