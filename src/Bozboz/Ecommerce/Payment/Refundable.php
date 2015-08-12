<?php namespace Bozboz\Ecommerce\Payment;

use Bozboz\Ecommerce\Order\Order;

interface Refundable
{
	public function refund(array $data, Order $order);
}
