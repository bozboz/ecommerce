<?php

namespace Bozboz\Ecommerce\Products;

use Breadcrumbs;
use Illuminate\Support\Facades\Validator as Validator;
use Illuminate\Support\Facades\Auth;

use Bozboz\Users\User;

use Bozboz\Ecommerce\Order\Order;
use Bozboz\Ecommerce\Order\Item;
use Bozboz\Ecommerce\Order\Orderable;
use Bozboz\Ecommerce\Order\Exception;
use Bozboz\Ecommerce\Order\OrderableException;

class OrderableProduct extends Product implements Orderable
{
	public function items()
	{
		return $this->morphMany('Bozboz\Ecommerce\Order\Item', 'orderable');
	}

	public function canAdjustQuantity()
	{
		return true;
	}

	public function canDelete()
	{
		return true;
	}

	public function validate($quantity, Item $item, Order $order)
	{
		$validation = Validator::make(
			array('stock' => $quantity),
			array('stock' => 'numeric|max:' . $this->stock_level),
			array('max' => 'Sorry. Some or all of the requested items are out of stock')
		);

		if ($validation->fails()) {
			if ($validation->errors()->first('stock') && $this->stock_level) {
				$item->quantity = $this->stock_level;
				$item->total_weight = $this->calculateWeight($item->quantity);
				$item->calculateNet($this, $order);
				$item->calculateGross();
				$item->save();
			}
			throw new OrderableException($validation);
		}
	}

	public function calculatePrice($quantity, Order $order)
	{
		return $quantity * $this->price * 100 / (1 + $this->tax_rate);
	}

	public function calculateWeight($quantity)
	{
		return $this->weight * $quantity;
	}

	public function label()
	{
		if ($parent = $this->variationOf) {
			$attributeOptions = $this->attributeOptions()->lists('value');
			return sprintf('%s (%s)', $parent->name, implode(' ', $attributeOptions));
		} else {
			return $this->name;
		}
	}

	public function image()
	{
		$model = $this->variation_of_id ? $this->transformToVariantObject() : $this;

		$media = $this->media()->first();

		return $media ? $media->getFilename('product_listing') : '/assets/images/listing-thumb-placeholder.jpg';
	}

	/**
	 * Return current model as a ProductVariant instance.
	 *
	 * @return Bozboz\Ecommerce\Products\ProductVariant
	 */
	private function transformToVariantObject()
	{
		$variant = new ProductVariant;
		$variant->setRawAttributes($this->getAttributes());

		return $variant;
	}

	public function calculateAmountToRefund(Item $item, $quantity)
	{
		return $item->price_pence_ex_vat * $quantity;
	}

	public function isTaxable()
	{
		return ! $this->tax_exempt;
	}

	/**
	 * Returns an array with properties which must be indexed
	 *
	 * @return array
	 */
	public function getSearchableBody()
	{
		return [
			'title' => $this->name,
			'url' => route('product-detail', $this->slug),
			'content' => $this->description,
			'image' => $this->image(),
			'breadcrumbs' => Breadcrumbs::render('product-detail', $this->name),
            'membership_types' => [],

			'price' => $this->getPriceForUser(),
			'data' => $this->toArray(),
		];
	}
}
