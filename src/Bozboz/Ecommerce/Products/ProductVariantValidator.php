<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Services\Validators\Validator;

class ProductVariantValidator extends Validator
{
	protected $editRules = array(
		'name' => 'required'
	);
}
