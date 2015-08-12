<?php

namespace Bozboz\Ecommerce\Http\Controllers\Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Ecommerce\Shipping\ShippingMethodDecorator;
use Bozboz\Admin\Reports\Report;

class ShippingMethodController extends ModelAdminController
{
	public function __construct(ShippingMethodDecorator $decorator)
	{
		parent::__construct($decorator);
	}

	public function index()
	{
		$report = new Report($this->decorator);

		$report->overrideView('ecommerce::shipping.admin.overview');

		return $report->render([
			'controller' => get_class($this)
		]);
	}
}
