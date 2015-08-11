<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Ecommerce\Order\Item;

class ProductPurchasedEvent
{
	public function handle(Item $item)
	{
		$item->orderable->decrement('stock_level', $item->quantity);
	}
}
