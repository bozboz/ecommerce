<?php

namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Ecommerce\Order\Order;
use Bozboz\Ecommerce\Shipping\OrderableShippingMethod;
use Illuminate\Support\Collection;

/**
 * The mailman class is responsible for setting and retrieving shipping methods
 * on an order
 */
class Mailman
{
	/**
	 * @var Bozboz\Ecommerce\Shipping\OrderableShippingMethod
	 */
	protected $shipping;

	public function __construct(OrderableShippingMethod $shipping)
	{
		$this->shipping = $shipping;
	}

	/**
	 * Get valid delivery methods valid in given $country
	 *
	 * @param  Bozboz\Ecommerce\Order\Order  $order
	 * @param  string  $country
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getValidDeliveryMethods(Order $order, $country)
	{
		$itemsPerBand = $order->items()
			->where('orderable_type', '!=', get_class($this->shipping))
			->with('orderable')->get()->groupBy(function($item) {
			return $item->orderable->shipping_band_id;
		});

		foreach($itemsPerBand as $band => $items) {
			$methods[$band] = $this->getMethodsForBand(
				$band,
				$country,
				(new Collection($items))->sum('total_weight')
			);
		}

		return $methods;
	}

	/**
	 * Retrieve valid methods for given band (by ID), based on country and weight
	 *
	 * @param  int     $band
	 * @param  string  $country
	 * @param  int     $weight
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	protected function getMethodsForBand($band, $country, $weight)
	{
		$validForCountry = $this->shipping->whereShippingBandId($band)->whereHas('costs', function($q) use ($country, $weight) {
			$q->location($country)->weight($weight);
		})->get();

		if ($validForCountry->count()) return $validForCountry;

		return $this->shipping->whereShippingBandId($band)->whereHas('costs', function($q) use ($weight) {
			$q->whereRegion('WW')->weight($weight);
		})->get();
	}

	/**
	 * Get current delivery method selected on given $order
	 *
	 * @param  Bozboz\Ecommerce\Order\Order  $order
	 * @return Bozboz\Ecommerce\Shipping\OrderableShippingMethod
	 */
	public function getCurrentDeliveryMethods(Order $order)
	{
		return $this->shipping->whereHas('items', function($q) use ($order) {
			$q->where('order_id', $order->id);
		})->get();
	}

	/**
	 * Set the given $shipping method on the given $order
	 *
	 * @param  Bozboz\Ecommerce\Order\Order  $order
	 * @param  Bozboz\Ecommerce\Shipping\OrderableShippingMethod  $shipping
	 * @return void
	 */
	public function setShippingMethod(Order $order, OrderableShippingMethod $shipping)
	{
		$this->deleteExistingShippingItem($order, $shipping);

		$order->addItem($shipping);
	}

	/**
	 * Delete any shipping items that are of the same shipping band as $shipping
	 * in $order.
	 *
	 * @param  Bozboz\Ecommerce\Order\Order  $order
	 * @param  Bozboz\Ecommerce\Shipping\OrderableShippingMethod  $shipping
	 * @return boolean
	 */
	protected function deleteExistingShippingItem(Order $order, OrderableShippingMethod $shipping)
	{
		foreach($order->items()->with('orderable')->get() as $item) {
			if ($item->orderable instanceof $shipping &&
			    $item->orderable->shipping_band_id == $shipping->shipping_band_id &&
			  ! $item->orderable->canAdjustQuantity()) {
				$item->delete();
			}
		}
	}

	/**
	 * Find shipping method by ID
	 *
	 * @param  int  $id
	 * @return Bozboz\Ecommerce\Shipping\OrderableShippingMethod
	 */
	public function findShippingMethod($id)
	{
		return $this->shipping->findOrFail($id);
	}
}
