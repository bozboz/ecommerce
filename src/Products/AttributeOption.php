<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Models\Base;

class AttributeOption extends Base
{
	protected $table = 'product_attribute_options';
	protected $fillable = ['value', 'product_attribute_id'];

	public function attribute()
	{
		return $this->belongsTo('Bozboz\Ecommerce\Products\Attribute', 'product_attribute_id');
	}

	public function products()
	{
		return $this->belongsToMany(
			'Bozboz\Ecommerce\Products\Product',
			'product_product_attribute_option',
			'product_id',
			'product_attribute_option_id'
		)->withTimestamps();
	}

	public function getValidator()
	{

	}
}
