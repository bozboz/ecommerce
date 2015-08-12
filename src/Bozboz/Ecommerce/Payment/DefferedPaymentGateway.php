<?php

namespace Bozboz\Ecommerce\Payment;

use Bozboz\Ecommerce\Order\Exception;
use Bozboz\Ecommerce\Order\Order;
use Illuminate\Validation\Factory as Validator;

class DefferedPaymentGateway implements PaymentGateway
{
	public function __construct(Validator $validator)
	{
		$this->validator = $validator;
	}

	public function purchase($data, Order $order)
	{
		$validation = $this->validator->make($data, [
			'purchase_order' => 'required'
		]);

		if ($validation->fails()) throw new Exception($validation);
	}
}
