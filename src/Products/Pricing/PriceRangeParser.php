<?php

namespace Bozboz\Ecommerce\Products\Pricing;

use Illuminate\Database\Eloquent\Builder;

class PriceRangeParser
{
	protected $input;

	/**
	 * Accepts a string input to be parsed
	 *
	 * @param  string  $input
	 */
	public function __construct($input)
	{
		$this->input = $input;
	}

	/**
	 * Build up query using $builder based on $this->input
	 *
	 * @param  Illuminate\Database\Eloquent\Builder  $builder
	 * @return Illuminate\Database\Eloquent\Builder
	 */
	public function filter(Builder $builder)
	{
		if (substr($this->input, 0, 1) == '<') {
			return $this->lessThan($builder, substr($this->input, 1));
		} elseif (substr($this->input, 0, 1) == '>') {
			return $this->greaterThan($builder, substr($this->input, 1));
		} elseif (strpos($this->input, '-')) {
			return $this->between($builder, explode('-', $this->input));
		} elseif (is_numeric($this->input) && !empty($this->input)) {
			return $this->exact($builder, $this->input);
		}
	}

	protected function lessThan($query, $number)
	{
		return $query->where('price_pence', '<', $number * 100);
	}

	protected function greaterThan($query, $number)
	{
		return $query->where('price_pence', '>', $number * 100);
	}

	protected function between($query, array $numbers)
	{
		return $query->whereBetween('price_pence', array_map(function($number) {
			return $number * 100;
		}, $numbers));
	}

	protected function exact($query, $number)
	{
		return $query->where('price_pence', $number * 100);
	}
}
