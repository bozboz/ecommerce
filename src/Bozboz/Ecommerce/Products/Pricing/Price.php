<?php

namespace Bozboz\Ecommerce\Products\Pricing;

use Bozboz\Admin\Models\Base;
use Bozboz\Users\User;

class Price extends Base
{
	use PriceTrait;

	const COST_FREE = 1;
	const COST_EMAIL_REQUIRED = 2;
	const COST_PAID = 3;

	protected $table = 'product_prices';

	protected $fillable = [
		'price',
		'product_id',
		'product_type',
		'membership_type_id'
	];

	public function product()
	{
		return $this->belongsTo('Bozboz\Ecommerce\Products\Product');
	}

	public function getValidator()
	{
		return new PriceValidator;
	}

	public function getRawPriceField()
	{
		return 'price_pence';
	}

	public function getDisplayPrice()
	{
		if ($this->price > 0) {
			return '£' . $this->price;
		}

		return 'Free';
	}

	/**
	 * Filter a product's prices by the membership type of given user
	 *
	 * @param  Illuminate\Database\Eloquent\Query  $query
	 * @param  Bozboz\Users\User|null  $user
	 * @return void
	 */
	public function scopeForUser($query, User $user = null)
	{
		$query->whereMembershipTypeId($user->membership_type_id);
	}

	static public function getCosts()
	{
		return [
			static::COST_FREE => 'Free',
			static::COST_EMAIL_REQUIRED => 'Email Signup Required',
			static::COST_PAID => 'Paid',
		];
	}
}
