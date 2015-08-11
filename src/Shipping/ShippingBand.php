<?php

namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Admin\Models\Base;
use Bozboz\StandardValidator;

class ShippingBand extends Base
{
	protected $fillable = ['name'];

	public function getValidator()
	{
		return new StandardValidator;
	}

	public function methods()
	{
		return $this->hasMany(ShippingMethod::class);
	}

	public function orderableMethods()
	{
		return $this->hasMany(OrderableShippingMethod::class);
	}
}
