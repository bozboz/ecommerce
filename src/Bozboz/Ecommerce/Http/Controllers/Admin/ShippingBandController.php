<?php

namespace Bozboz\Ecommerce\Http\Controllers\Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Report;
use Bozboz\Ecommerce\Shipping\ShippingBandDecorator;

class ShippingBandController extends ModelAdminController
{
	public function __construct(ShippingBandDecorator $decorator)
	{
		parent::__construct($decorator);
	}
}
