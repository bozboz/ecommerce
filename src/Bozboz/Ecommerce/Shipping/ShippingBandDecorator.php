<?php

namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Admin\Decorators\ModelAdminDecorator;

class ShippingBandDecorator extends ModelAdminDecorator
{
	public function __construct() {}

	public function getFields($instance)
	{
		return [];
	}

	public function getLabel($instance)
	{
		return $instance->name;
	}
}
