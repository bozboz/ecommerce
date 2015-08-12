<?php namespace Bozboz\Ecommerce\Address;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
	protected $guarded = ['id', 'created_at', 'updated_at'];
	protected $hidden = ['created_at', 'updated_at', 'pivot'];

	public function customers()
	{
		return $this->belongsToMany('Bozboz\Ecommerce\Customer\Customer');
	}

	public function parts()
	{
		$attributes = $this->toArray();

		return array_except($attributes, 'id');
	}
}
