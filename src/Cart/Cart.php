<?php namespace Bozboz\Ecommerce\Cart;

use Bozboz\Ecommerce\Voucher\OrderableVoucher;
use Bozboz\Ecommerce\Order\Order;
use Bozboz\Ecommerce\Order\Item;
use Bozboz\Ecommerce\Order\Orderable;

class Cart extends Order
{
	protected $table = 'orders';

	/**
	 * Add $orderable to the cart, with an optional quantity
	 *
	 * @param  Bozboz\Ecommerce\Order\Orderable  $orderable
	 * @param  int  $quantity
	 * @return void
	 */
	public function add(Orderable $orderable, $quantity = 1)
	{
		if ($item = $this->contains($orderable)) {
			if ($orderable->canAdjustQuantity()) {
				$this->updateItem($item, $item->quantity + $quantity);
			} else {
				$this->remove($item);
				$item = $this->addItem($orderable, $quantity);
			}
			static::$dispatcher->fire('cart.item.updated', $item);
		} else {
			$item = $this->addItem($orderable, $quantity);
			static::$dispatcher->fire('cart.item.added', $item);
		}

		$item->save();

		return $item;
	}

	/**
	 * Add voucher with $voucherCode to cart
	 *
	 * @param  string  $voucherCode
	 * @return void
	 */
	public function addVoucher($voucherCode)
	{
		$voucher = OrderableVoucher::whereCode($voucherCode)->firstOrFail();

		if ( ! $this->contains($voucher)) {
			$this->order->addItem($voucher);
		}
	}

	/**
	 * Remove/adjust quantity of $item from the cart
	 *
	 * @param  Bozboz\Ecommerce\Order\Item  $item
	 * @param  int  $quantity
	 * @return void
	 */
	public function remove(Item $item, $quantity = 0)
	{
		if ($quantity <= 0) {
			$item->delete();
			static::$dispatcher->fire('cart.item.removed', $item);
		} else {
			$this->updateItem($item, $item->quantity - $quantity);
			static::$dispatcher->fire('cart.item.updated', $item);
			$item->save();
		}
	}

	/**
	 * Remove/adjust quantity of item from the cart with given $id and $quantity
	 *
	 * @param  int  $id
	 * @param  int  $quantity
	 * @return void
	 * @throws Illuminate\Database\Eloquent\ModelNotFoundException
	 */
	public function removeById($id, $quantity = 0)
	{
		$this->remove($this->items()->find($id), $quantity);
	}

	/**
	 * Update quantites of existing items based on given $quantities
	 *
	 * @param  array  $quantities
	 * @return void
	 */
	public function updateQuantities(array $quantities)
	{
		foreach($this->items as $item) {
			$quantity = $quantities[$item->id];
			if (isset($quantity)) {
				static::$dispatcher->fire('cart.item.updated', $item);
				if ($quantity === '0') {
					$this->removeById($item->id);
				} else {
					$this->updateItem($item, $quantity);
					$item->save();
				}
			}
		}
	}

	/**
	 * Return all items from the cart
	 *
	 * @return void
	 */
	public function clearItems()
	{
		$this->items()->delete();

		static::$dispatcher->fire('cart.items.cleared', $this);
	}

	public function getId()
	{
		return $this->order->getKey();
	}
}
