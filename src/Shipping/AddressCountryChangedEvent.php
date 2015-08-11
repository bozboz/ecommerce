<?php

namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Ecommerce\Address\Address;
use Bozboz\Ecommerce\Order\Order;

class AddressCountryChangedEvent
{
	public function handle(Address $address, Order $order)
	{
		$order->items()
			->where('orderable_type', OrderableShippingMethod::class)
			->delete();
	}
}
