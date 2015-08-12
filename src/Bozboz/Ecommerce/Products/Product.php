<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Users\User;
use Bozboz\Users\Membership\MemberPricesTrait;
use Bozboz\Admin\Models\Base;
use Bozboz\Admin\Traits\DynamicSlugTrait;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidationFactory;
use Bozboz\MediaLibrary\Models\MediableTrait;

class Product extends Base
{
	protected $table = 'products';

	use MediableTrait, DynamicSlugTrait;

	protected $fillable = [
		'name',
		'slug',
		'description',
		'category_id',
		'requires_email_signup_for_non_members',
		'variation_of_id',
		'stock_level',
		'weight',
		'sku',
		'nominal_code',
		'status',
		'tax_exempt',
		'shipping_band_id',

		'price',
		'price_includes_tax',
		'prices_data',
		'new_prices_data',
	];

	protected $nullable = [
		'manufacturer_id',
		'category_id',
		'variation_of_id'
	];

	public function getValidator()
	{
		return new ProductValidator;
	}

	public function getRawPriceField()
	{
		return 'price_pence';
	}

	public function getSlugSourceField()
	{
		return 'name';
	}

	public function attributeOptions()
	{
		return $this->belongsToMany(
			'Bozboz\Ecommerce\Products\AttributeOption',
			'product_product_attribute_option',
			'product_id',
			'product_attribute_option_id'
		)->withTimestamps();
	}

	public function relatedProducts()
	{
		return $this->belongsToMany(get_class($this), 'related_products', 'product_id', 'related_product_id')->withTimestamps();
	}

	public function scopeActive($query)
	{
		return $query->where('status', 1);
	}

	public function scopeVisible($query)
	{
		return $query->whereNull('variation_of_id')->with('variants');
	}

	public function scopeByPrice($query, $price)
	{
		$priceRange = new Pricing\PriceRangeParser($price);
		return $priceRange->filter($query);
	}

	public function scopeSearch($query, $searchTerm)
	{
		$query->where('name', 'LIKE', '%' . $searchTerm . '%');
	}

	public function manufacturer()
	{
		return $this->belongsTo('Bozboz\Ecommerce\Products\Manufacturer');
	}

	public function category()
	{
		return $this->belongsTo('Bozboz\Ecommerce\Products\Category');
	}

	public function categories()
	{
		return $this->belongsToMany('Bozboz\Ecommerce\Products\Category', 'category_product', 'product_id', 'category_id');
	}

	public function variants()
	{
		return $this->hasMany('Bozboz\Ecommerce\Products\ProductVariant', 'variation_of_id');
	}

	public function variationOf()
	{
		return $this->belongsTo(get_class($this), 'variation_of_id');
	}

	public function shippingBand()
	{
		return $this->belongsTo('Bozboz\Ecommerce\Shipping\ShippingBand');
	}

	public function getVariantsList()
	{
		$list = [];
		$variants = $this->variants()->with('attributeOptions')->get();

		foreach($variants as $variant) {
			$list[$variant->id] = [
				'label' => $variant->variantLabel(),
				'is_available' => $variant->isAvailable(),
			];
		}

		return $list;
	}

	/**
	 * Mutator method for setting tax rate based on price_includes_tax attribute
	 *
	 * @param  boolean  $value
	 */
	public function setPriceIncludesTaxAttribute($value)
	{
		$this->tax_rate = $value ? 0.2 : 0;
	}

	/**
	 * Access method to determine if price includes tax
	 *
	 * @return boolean
	 */
	public function getPriceIncludesTaxAttribute()
	{
		return $this->tax_rate > 0;
	}

	/*
	 * @param  Bozboz\Users\User|null  $user
	 * @return boolean
	 */
	public function isAvailable(User $user = null)
	{
		$stockLevel = 0;

		if ($this->variants->count()) {
			foreach($this->variants as $variant) {
				$stockLevel += $variant->stock_level;
			}
		} else {
			$stockLevel = $this->stock_level;
		}

		return $stockLevel > 0;
	}
}
