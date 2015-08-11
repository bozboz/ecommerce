<?php namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Admin\Services\Validators\Validator;

class ShippingMethodValidator extends Validator
{
	protected $rules = [
		'name' => 'required',
		'shipping_band_id' => 'required'
	];
}
