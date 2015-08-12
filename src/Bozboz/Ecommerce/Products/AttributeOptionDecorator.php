<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Decorators\ModelAdminDecorator;

class AttributeOptionDecorator extends ModelAdminDecorator
{
	public function __construct(AttributeOption $model)
	{
		parent::__construct($model);
	}

	public function getLabel($instance)
	{
		return $instance->attribute->name . ' - ' . $instance->value;
	}

	public function getFields($instance)
	{
		return [];
	}

	public function getColumns($instance)
	{
		return [];
	}
}
