<?php

namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Admin\Services\Validators\Validator;

class ShippingBandValidator extends Validator
{
	protected $rules = [
		'name' => 'required'
	];
}
