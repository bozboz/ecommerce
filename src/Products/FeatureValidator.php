<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Services\Validators\Validator;

class FeatureValidator extends Validator
{
	protected $rules = array(
		'name' => 'required',
		'slug' => 'required'
	);
}
