<?php namespace Bozboz\Ecommerce\Cart;

use Illuminate\Session\Store as Session;

class SessionStorage implements CartStorageInterface
{
	protected $session;

	public function __construct(Session $session)
	{
		$this->session = $session;
	}

	public function getCart($required = false)
	{
		$cart = Cart::whereId($this->session->get('cart'))->whereHas('state', function($q)
		{
			$q->where('name', 'LIKE', 'Cart%');

		})->first();

		if ($required && ! $cart) throw new CartMissingException;

		return $cart;
	}

	public function getOrCreateCart()
	{
		$cart = null;

		if ($this->session->has('cart')) {
			$cart = $this->getCart();
		}

		if ( ! $cart) {
			$cart = Cart::create(['state_id' => 1]);
			$this->session->put('cart', $cart->getKey());
		}

		return $cart;
	}

	public function getCartOrFail()
	{
		return $this->getCart(true);
	}
}
