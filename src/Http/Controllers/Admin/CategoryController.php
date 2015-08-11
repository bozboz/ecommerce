<?php

namespace Bozboz\Ecommerce\Http\Controllers\Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Report;
use Bozboz\Ecommerce\Products\CategoryDecorator;
use Bozboz\Ecommerce\Products\ProductDecorator;
use Bozboz\Ecommerce\Products\Product;
use Illuminate\Support\Facades\View;
use Bozboz\Admin\Reports\NestedReport;

class CategoryController extends ModelAdminController
{
	public function __construct(CategoryDecorator $decorator)
	{
		parent::__construct($decorator);
	}

	public function index()
	{
		$report = new NestedReport($this->decorator);

		return $report->render([
			'controller' => get_class($this)
		]);
	}

	protected function getProductsReport($parentCategory = null)
	{
		$decorator = new ProductDecorator(new Product);

		if (!is_null($parentCategory)) {
			$decorator->setCategory($parentCategory);
		}

		return new Report($decorator, new ProductController($decorator));
	}
}
