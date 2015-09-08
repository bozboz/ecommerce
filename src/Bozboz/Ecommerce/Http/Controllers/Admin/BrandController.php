<?php

namespace Bozboz\Ecommerce\Http\Controllers\Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Ecommerce\Products\BrandDecorator;

class BrandController extends ModelAdminController
{
	public function __construct(BrandDecorator $decorator)
	{
		parent::__construct($decorator);
	}
}
