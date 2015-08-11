<?php

namespace Bozboz\Ecommerce\Http\Controllers\Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Ecommerce\Products\ProductDecorator;

class ProductController extends ModelAdminController
{
	public function __construct(ProductDecorator $decorator)
	{
		parent::__construct($decorator);
	}
}
