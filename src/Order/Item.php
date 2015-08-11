<?php namespace Bozboz\Ecommerce\Order;

use Bozboz\Ecommerce\Order\Orderable;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	protected $table = 'order_items';
	protected $fillable = [
		'name',
		'price_pence_ex_vat',
		'price_pence',
		'quantity',
		'tax_rate',
		'total_tax_pence',
		'total_price_pence',
		'total_price_pence_ex_vat',
		'total_weight',
		'image'
	];

	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function orderable()
	{
		return $this->morphTo();
	}

	/**
	 * Calculate the net prices, based on given Orderable and Order instances
	 *
	 * @param  Bozboz\Ecommerce\Order\Orderable  $orderable
	 * @param  Bozboz\Ecommerce\Order\Order  $order
	 * @return void
	 */
	public function calculateNet(Orderable $orderable, Order $order)
	{
		$this->price_pence_ex_vat = $orderable->calculatePrice(1, $order);
		$this->total_price_pence_ex_vat = $orderable->calculatePrice($this->quantity, $order);
	}

	/**
	 * Calculate the gross prices off the net fields and the tax_rate
	 *
	 * @return void
	 */
	public function calculateGross()
	{
		$this->price_pence = $this->price_pence_ex_vat * (1 + $this->tax_rate);
		$this->total_tax_pence = $this->total_price_pence_ex_vat * $this->tax_rate;
		$this->total_price_pence = $this->total_price_pence_ex_vat + $this->total_tax_pence;
	}

	/**
	 * Generate a full or partial refunded order item of the current
	 *
	 * @param  int|null  $partialQuantity
	 * @return Bozboz\Ecommerce\Order\Item
	 */
	public function generateRefundedItem($partialQuantity = null)
	{
		$refundedItem = $this->replicate();

		$refundedItem->quantity = $partialQuantity ?: $this->quantity;

		$refundedItem->price_pence_ex_vat = $this->orderable->calculateAmountToRefund($this, 1);
		$refundedItem->total_price_pence_ex_vat = -$this->orderable->calculateAmountToRefund($this, $refundedItem->quantity);

		$refundedItem->calculateGross();

		return $refundedItem;
	}
}
