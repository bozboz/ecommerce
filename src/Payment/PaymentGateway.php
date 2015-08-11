<?php namespace Bozboz\Ecommerce\Payment;

use Bozboz\Ecommerce\Order\Order;

interface PaymentGateway
{
	public function purchase($data, Order $order);
}
