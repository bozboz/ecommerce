<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Services\Validators\Validator;

class ProductValidator extends Validator
{
	protected $rules = [
		'name' => 'required',
		'weight' => 'min:1'
	];

	protected $editRules = [
		'slug' => 'required'
	];
}
