<?php

namespace Bozboz\Ecommerce\Products\Pricing;

trait PriceTrait
{
	/**
	 * Attribute to store the raw database price in
	 *
	 * @return string
	 */
	abstract public function getRawPriceField();

	/**
	 * Get a formatted, consistent whole-pound price value
	 *
	 * @return string
	 */
	public function getPriceAttribute()
	{
		$priceField = $this->getRawPriceField();

		return number_format($this->$priceField / 100, 2, '.', '');
	}

	/**
	 * Set the raw price based on a formatted, whole-pound price value
	 *
	 * @param  int
	 * @return void
	 */
	public function setPriceAttribute($price)
	{
		$this->attributes[$this->getRawPriceField()] = $price * 100;
	}
}
