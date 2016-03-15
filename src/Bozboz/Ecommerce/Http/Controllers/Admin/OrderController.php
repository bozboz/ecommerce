<?php

namespace Bozboz\Ecommerce\Http\Controllers\Admin;

use View, Redirect, Input, Response;
use Bozboz\Admin\Controllers\ModelAdminController;
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
		$data = [];

		$report = new Report($this->decorator);

		if ($report->hasRows()) {
			$data[] = $report->getHeadings();
			foreach($report->getRows() as $row) {
				$data[] = $row->getColumns();
			}

			array_walk_recursive($data, function(&$item) {
				$item = strip_tags($item);
			});
		}

		$csv = public_path('csv/file.csv');

		$fp = fopen($csv, 'w');

		foreach ($data as $fields) {
			fputcsv($fp, $fields);
		}

		return Response::download($csv, sprintf('sales-report_%s.csv', date('Y-m-d_H:i')));
	}
}
