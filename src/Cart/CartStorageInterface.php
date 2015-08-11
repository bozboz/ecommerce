<?php namespace Bozboz\Ecommerce\Cart;

interface CartStorageInterface
{
	/**
	 * Retrieve Cart instance
	 *
	 * @param  boolean  $required
	 * @throws CartMissingException
	 * @return Bozboz\Ecommerce\Cart\Cart
	 */
	public function getCart($required = false);

	/**
	 * Retrieve, or create a Cart instance
	 *
	 * @return Bozboz\Ecommerce\Cart\Cart
	 */
	public function getOrCreateCart();
}
