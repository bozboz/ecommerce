<?php namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Admin\Services\Validators\Validator;

class ShippingCostValidator extends Validator
{
	protected $rules = [
		'shipping_method_id' => 'required',
		'country' => '',
		'region' => 'required_without:country',
		'from_weight' => 'numeric',
		'price' => 'required|numeric'
	];
}
