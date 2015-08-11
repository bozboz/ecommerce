<?php

namespace Bozboz\Ecommerce\Http\Controllers;

use Bozboz\Ecommerce\Products\Category;
use Bozboz\Ecommerce\Products\Feature;
use Bozboz\Ecommerce\Products\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShopController extends Controller
{
	public function index()
	{
		$page = Page::whereSlug('store')->first();

		return $this->productListing(Feature::first()->products()->visible()->getQuery())->with([
			'heading' => $page->title,
			'title' => $page->meta_title
		]);
	}

	public function search()
	{
		$query = Input::get('q');
		$results = Product::visible()->search($query);

		return $this->productListing($results)->with([
			'heading' => sprintf('Search results for "%s"', $query),
			'title' => sprintf('Search results for "%s"', $query)
		]);
	}

	public function filter($filter)
	{
		$products = Product::visible()->whereHas('manufacturer', function($q) use ($filter) {
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
		$product = Product::where('slug', $productSlug)->firstOrFail();
		$category = $product->categories()->whereSlug($categorySlug)->firstOrFail();

		$product->category = $category;
		return $this->productDetail($product);
	}

	public function productOrCategory($slug)
	{
		if ($product = Product::where('slug', $slug)->first()) {
			return $this->productDetail($product)->with([
				'category' => $product->category
			]);
		}

		if ($category = Category::where('slug', $slug)->first()) {
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

		return View::make('products.listing')->with([
			'items' => $builder->paginate(12),
			'user' => Auth::user(),
			'detailUrl' => 'product-detail',
			'description' => '',
		]);
	}

	protected function productDetail(Product $product)
	{
		if ($product->variants->count()) {
			$media = Media::forCollection($product->variants)->get();
		} else {
			$media = Media::forModel($product)->get();
		}

		return View::make('products.product')->with([
			'product' => $product,
			'available' => $product->isAvailable(),
			'media' => $media,
			'title' => $product->meta_title
		]);
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
