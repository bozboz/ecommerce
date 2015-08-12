<?php

namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Admin\Decorators\ModelAdminDecorator;
use Bozboz\Admin\Fields\TextField;

class ShippingBandDecorator extends ModelAdminDecorator
{
	public function __construct(ShippingBand $model)
	{
		parent::__construct($model);
	}

	public function getFields($instance)
	{
		return [
			new TextField('name'),
		];
	}

	public function getLabel($instance)
	{
		return $instance->name;
	}
}
