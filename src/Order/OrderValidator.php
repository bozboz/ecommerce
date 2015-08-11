<?php

namespace Bozboz\Ecommerce\Order;

use Bozboz\Admin\Services\Validators\Validator;

class OrderValidator extends Validator
{
	protected $editRules = [
		'state_id' => 'required|exists:order_states,id'
	];
}
