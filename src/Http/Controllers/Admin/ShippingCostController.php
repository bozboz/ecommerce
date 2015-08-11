<?php

namespace Bozboz\Ecommerce\Http\Controllers\Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Ecommerce\Shipping\ShippingCostDecorator;

use Illuminate\Support\Facades\Redirect;

class ShippingCostController extends ModelAdminController
{
	public function __construct(ShippingCostDecorator $decorator)
	{
		parent::__construct($decorator);
	}

	public function index()
	{
		return Redirect::route('admin.shipping.index');
	}

	public function createForMethod($methodId)
	{
	    $instance = $this->decorator->newModelInstance(['method' => $methodId]);

	    return $this->renderCreateFormFor($instance);
	}
}
