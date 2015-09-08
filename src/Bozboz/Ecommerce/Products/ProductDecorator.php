<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Decorators\ModelAdminDecorator;
use Bozboz\Admin\Fields\BelongsToField;
use Bozboz\Admin\Fields\BelongsToManyField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\HTMLEditorField;
use Bozboz\Admin\Fields\SelectField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\URLField;
use Bozboz\Admin\Reports\Filters\ArrayListingFilter;
use Bozboz\Admin\Reports\Filters\SearchListingFilter;
use Bozboz\Ecommerce\Fields\PriceField;
use Bozboz\Ecommerce\Shipping\ShippingBandDecorator;
use Bozboz\MediaLibrary\Fields\MediaBrowser;
use Bozboz\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\HTML;

class ProductDecorator extends ModelAdminDecorator
{
	protected $categoryDecorator;
	protected $attributeDecorator;
	protected $shippingDecorator;

	public function __construct(
		OrderableProduct $model,
		CategoryDecorator $categoryDecorator,
		AttributeOptionDecorator $attributeDecorator,
		ShippingBandDecorator $shippingDecorator
	)
	{
		$this->categoryDecorator = $categoryDecorator;
		$this->attributeDecorator = $attributeDecorator;
		$this->shippingDecorator = $shippingDecorator;

		parent::__construct($model);
	}

	public function getColumns($product)
	{
		return array(
			'ID' => sprintf('<span class="id">#%s</span>', str_pad($product->id, 3, '0', STR_PAD_LEFT)),
			'Name' => $product->name,
			'Variants' => $product->exists ? $this->linkToVariants($product->variants) : null,
			'Category' => $product->exists ? $this->linkToCategory($product->categories) : null,
			'Price' => format_money($product->price_pence),
			'Stock Level' => $product->exists ? (count($product->variants) ? '-' : $product->stock_level) : null,
			'Added' => $product->created_at ? sprintf('<abbr title="%s">%s</a>',
				$product->created_at->format('jS F Y'),
				$product->created_at->diffForHumans()
			) : null
		);
	}

	private function linkToVariants($variants)
	{
		$links = [];

		foreach($variants as $variant) {
			$links[] = '- ' . HTML::linkAction(
				'Admin\ProductController@edit',
				implode(' ', $variant->attributeOptions->lists('value')) . ' (' . $variant->stock_level . ')',
				[ $variant->id ]
			);
		}

		return implode('<br>', $links);
	}

	private function linkToCategory($categories)
	{
		$links = [];

		foreach($categories as $category) {
			$links[] = '- ' . HTML::linkAction(
				'Admin\CategoryController@edit',
				$category->name,
				[ $category->id ]
			);
		}

		return implode('<br>', $links);
	}

	protected function modifyListingQuery(Builder $query)
	{
		$query->whereNull('variation_of_id');

		parent::modifyListingQuery($query);
	}

	public function getListingFilters()
	{
		return [
			new ArrayListingFilter('category', $this->getCategoryList(), function($builder, $value) {
				if ($value) {
					$builder->whereHas('categories', function($q) use ($value) {
						$q->where('categories.id', $value);
					});
				}
			}),
			new ArrayListingFilter('stock_level', $this->getStockLevelList(), function($builder, $value){
				switch ($value) {
					case 'in_stock':
						$builder->where('stock_level', '>', 0);
					break;

					case 'out_of_stock':
						$builder->where(function($q){
							$q->whereNull('stock_level')
							  ->orWhere('stock_level', '=', 0);
						});
					break;
				}
			}),
			new SearchListingFilter('search', ['sku', 'name'])
		];
	}

	private function getCategoryList()
	{
		return [ null => 'All' ] + $this->model->category()->getRelated()->orderBy('parent_id')->orderBy('name')->lists('name', 'id');
	}

	private function getStockLevelList()
	{
		return [
			null => 'All',
			'out_of_stock' => 'Out of stock only',
			'in_stock' => 'In stock only',
		];
	}

	public function getListingQuery(Builder $query)
	{
		$query
			->whereVariationOfId(null)
			->with('categories', 'variants', 'variants.attributeOptions')
			->latest()
			->orderBy('name');
	}

	public function getLabel($product)
	{
		$variantLabel = $product->variation_of_id ? ' (' . implode(', ', $product->attributeOptions->lists('value')) . ')' : '';
		return $product->name . $variantLabel;
	}

	public function getFields($instance)
	{
		$variationsOf = $this->model->whereNull('variation_of_id')->orderBy('name')->lists('name', 'id');

		$commonFields = [
			new TextField('name'),
			new SelectField(['name' => 'variation_of_id', 'label' => 'Variation of Product', 'options' => [null => '-'] + $variationsOf]),
			new CheckboxField('status'),
			new HTMLEditorField('description'),
			new PriceField('price', ['label' => 'Base Price']),
			new CheckboxField('requires_email_signup_for_non_members'),
			new CheckboxField('tax_exempt'),
			new TextField('stock_level'),
			new BelongsToField($this->shippingDecorator, $instance->shippingBand()),
			new TextField('weight'),
			new TextField(['name' => 'sku', 'label' => 'SKU']),
			new MediaBrowser($instance->media()),
		];

		return array_merge($commonFields, $this->getAdditionalFields($instance));
	}

	protected function getAdditionalFields($instance)
	{
		if ($instance->variation_of_id) {
			$fields = [
				new BelongsToManyField(
					$this->attributeOptionDecorator, $instance->attributeOptions(), [ 'label' => 'Attributes' ], function($query) {
						$query->with('attribute')->orderBy('product_attribute_id')->orderBy('value');
					}
				)
			];
		} else {
			$fields = [
				new URLField('slug', Config::get('ecommerce::urls.products')),
				new BelongsToManyField(
					$this->categoryDecorator, $instance->categories(), ['label' => 'Categories']
				),
				new BelongsToManyField(
					$this, $instance->relatedProducts(), ['label' => 'Related Products'], function($query) {
						$query->visible();
					}
				)
			];
		}

		return $fields;
	}

	public function getSyncRelations()
	{
		return ['categories', 'relatedProducts', 'attributeOptions', 'media'];
	}

	/**
	 * Return a new instance of Product or ProductVariant, dependent on given
	 * $attributes array.
	 *
	 * @param  array  $attributes
	 * @return Bozboz\Ecommerce\Products\Product
	 */
	public function newModelInstance($attributes = array())
	{
		if ( ! empty($attributes['variation_of_id'])) {
			return (new ProductVariant)->newInstance($attributes);
		}

		return parent::newModelInstance($attributes);
	}

	/**
	 * Lookup instance by ID and return as Product or ProductVariant, dependent
	 * on given $attributes array.
	 *
	 * @param  int  $id
	 * @return Bozboz\Ecommerce\Products\Product
	 */
	public function findInstance($id)
	{
		$instance = parent::findInstance($id);

		if ( ! $instance->variation_of_id) return $instance;

		$variant = new ProductVariant;
		$variant->setRawAttributes($instance->getAttributes());
		$variant->exists = true;

		return $variant;
	}
}
