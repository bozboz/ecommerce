<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Models\Base;

class Feature extends Base
{
	protected $fillable = array('name', 'slug');

	public function products()
	{
		return $this->belongsToMany('Bozboz\Ecommerce\Products\Product', 'featured_products');
	}

	public function getValidator()
	{
		return new FeatureValidator;
	}
}
