<?php

namespace Bozboz\Ecommerce\Shipping;

use Illuminate\Support\Facades\Validator;
use Bozboz\Ecommerce\Orders\Orderable;
use Bozboz\Ecommerce\Orders\Order;
use Bozboz\Ecommerce\Orders\Item;

class OrderableShippingMethod extends ShippingMethod implements Orderable
{
	public $table = 'shipping_methods';

	public function items()
	{
		return $this->morphMany(Item::class, 'orderable');
	}

	public function canAdjustQuantity()
	{
		return false;
	}

	public function canDelete()
	{
		return false;
	}

	public function validate($quantity, Item $item, Order $order)
	{
	}

	public function calculatePrice($quantity, Order $order)
	{
		$country = $order->shippingAddress ? $order->shippingAddress->country : 'GB';

		$weight = $order->items()->with('orderable')->get()->filter(function($item) {
			return $item->orderable->shipping_band_id == $this->shipping_band_id;
		})->sum('total_weight');

		$countryPrice = $this->costs()->location($country)->weight($weight);
		$worldwidePrice = $this->costs()->whereRegion('WW')->weight($weight);

		$priceField = $countryPrice->getModel()->getRawPriceField();

		return $countryPrice->pluck($priceField) ?: $worldwidePrice->pluck($priceField);
	}

	public function calculateWeight($quantity)
	{
		return 0;
	}

	public function label()
	{
		return $this->name;
	}

	public function image()
	{
		return '/assets/images/shipping-item.png';
	}

	public function calculateAmountToRefund(Item $item, $quantity)
	{
		return $item->total_price_pence_ex_vat;
	}

	public function isTaxable()
	{
		return true;
	}
}
