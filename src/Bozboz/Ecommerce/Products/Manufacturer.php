<?php

namespace Bozboz\Ecommerce\Products;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
	protected $fillable = array('name', 'slug');

	public function product()
	{
		return $this->belongsTo('Bozboz\Ecommerce\Products\Product');
	}
}
