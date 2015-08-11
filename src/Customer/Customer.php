<?php namespace Bozboz\Ecommerce\Customer;

use Bozboz\Admin\Models\User;

class Customer extends User
{
	public function addresses()
	{
		return $this->belongsToMany('Bozboz\Ecommerce\Address\Address')->withTimestamps();
	}

	public function orders()
	{
		return $this->hasMany('Bozboz\Ecommerce\Order\Order', 'user_id');
	}
}
