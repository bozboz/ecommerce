<?php

namespace Bozboz\Ecommerce\Http\Controllers\Admin;

use View, Redirect, Input, Response;
use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\CSVReport;
use Bozboz\Admin\Reports\Report;
use Bozboz\Ecommerce\Order\OrderDecorator;
use Bozboz\Ecommerce\Order\Refund;
use Bozboz\Ecommerce\Payment\Exception as PaymentException;

class OrderController extends ModelAdminController
{
	protected $editView = 'ecommerce::orders.admin.edit';
	private $refund;

	public function __construct(OrderDecorator $decorator, Refund $refund)
	{
		$this->refund = $refund;

		parent::__construct($decorator);
	}

	public function index()
	{
		$report = new Report($this->decorator);
		$report->overrideView('ecommerce::orders.admin.overview');
		return $report->render(array(
			'controller' => get_class($this),
			'canCreate' => false
		));
	}

	public function refund($orderId)
	{
		$order = $this->decorator->findInstance($orderId);
		$errors = [];

		try {
			$this->refund->process($order, Input::get('items'));
		} catch (PaymentException $e) {
			$errors['refund'] = $e->getMessage();
		}

		return Redirect::back()->withErrors($errors);
	}

	public function downloadCsv()
	{
		$report = new CSVReport($this->decorator);
		return $report->render();
	}
}
