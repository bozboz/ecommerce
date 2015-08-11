<?php

namespace Bozboz\Ecommerce\Products;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
	protected $table = 'product_attributes';
	protected $fillable = ['name'];

	public function options()
	{
		return $this->hasMany('Bozboz\Ecommerce\Products\AttributeOption', 'product_attribute_id');
	}
}
