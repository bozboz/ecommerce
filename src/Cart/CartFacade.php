<?php namespace Bozboz\Ecommerce\Cart;

use Illuminate\Support\Facades\Facade;
use Bozboz\Ecommerce\Order\Order;

class CartFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'cart';
	}
}
