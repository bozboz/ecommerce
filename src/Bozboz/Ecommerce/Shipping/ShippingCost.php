<?php namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Admin\Models\Base;
use Bozboz\Ecommerce\Products\Pricing\PriceTrait;
use Illuminate\Support\Facades\DB;

class ShippingCost extends Base
{
	use PriceTrait;

	protected $fillable = ['country', 'region', 'from_weight', 'price', 'shipping_method_id'];

	public function getValidator()
	{
		return new ShippingCostValidator;
	}

	public function method()
	{
		return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
	}

	/**
	 * Find country (or associated region) specific costs
	 *
	 * @param  Illuminate\Database\Eloquent\Builder  $query
	 * @param  string  $country
	 * @return void
	 */
	public function scopeLocation($query, $country)
	{
		$region = $this->regionFromCountry($country);

		$query->where(function($q) use ($country, $region) {
			$q->where('country', $country)->orWhere('region', $region);
		});
	}

	/**
	 * Find weight-applicable costs
	 *
	 * @param  Illuminate\Database\Eloquent\Builder  $query
	 * @param  int  $weight
	 * @return void
	 */
	public function scopeWeight($query, $weight)
	{
		$query->where(function($q) use ($weight) {
			$q->whereNull('from_weight')->orWhere('from_weight', '<=', $weight);
		})->orderBy('from_weight', 'DESC');
	}

	/**
	 * Get the region of the passed in country string
	 *
	 * @param  string  $country
	 * @return string
	 */
	protected function regionFromCountry($country)
	{
		return DB::table('countries')->where('code', $country)->pluck('region');
	}


	/**
	 * Attribute to store the raw database price in
	 *
	 * @return string
	 */
	public function getRawPriceField()
	{
		return 'price_pence_ex_vat';
	}
}
