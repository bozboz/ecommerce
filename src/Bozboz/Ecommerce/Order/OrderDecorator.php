<?php namespace Bozboz\Ecommerce\Order;

use Bozboz\Admin\Decorators\ModelAdminDecorator;
use Bozboz\Admin\Fields\SelectField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Reports\Filters\ArrayListingFilter;
use Bozboz\Admin\Reports\Filters\SearchListingFilter;
use Bozboz\Ecommerce\ListingFilters\DateFilter;
use Illuminate\Database\Eloquent\Builder;
use Bozboz\Admin\Reports\Downloadable;

class OrderDecorator extends ModelAdminDecorator implements Downloadable
{
	const TODAY = 1;
	const THIS_WEEK = 2;
	const THIS_MONTH = 3;
	const PAST_WEEK = 4;
	const PAST_MONTH = 5;
	const PAST_QUARTER = 6;

	public function __construct(Order $model)
	{
		parent::__construct($model);
	}

	public function getColumns($order)
	{
		return array(
			'ID' => sprintf('<span class="id">#%s</span>', str_pad($order->id, 3, '0', STR_PAD_LEFT)),
			'Customer' => $order->customer_first_name . ' ' . $order->customer_last_name,
			'Country' => $order->billingAddress ? $order->billingAddress->country : '-',
			'Date' => $order->created_at,
			'Total' => format_money($order->totalPrice())
		);
	}

	public function getColumnsForCSV($order)
	{
		return [
			'id' => $order->id,
			'customer' => $order->customer_first_name . ' ' . $order->customer_last_name,
			'country' => $order->billingAddress ? $order->billingAddress->country : '-',
			'date' => $order->created_at,
			'total' => format_money($order->totalPrice())
		];
	}

	public function modifyListingQuery(Builder $query)
	{
		$query
			->with('billingAddress', 'items')
			->latest();
	}

	public function getListingFilters()
	{
		return [
			new DateFilter,
			new ArrayListingFilter('state', $this->getStateOptions(), 'state_id', 3),
			new SearchListingFilter('customer', [], function($q, $value) {
				foreach(explode(' ', $value) as $part) {
					$q->where(function($q) use ($part) {
						foreach(['customer_first_name', 'customer_last_name', 'customer_email'] as $attr) {
							$q->orWhere($attr, 'LIKE', "%$part%");
						}
					});
				}
			})
		];
	}

	protected function getStateOptions()
	{
		return [null => 'All'] + State::lists('name', 'id');
	}

	public function getLabel($model)
	{
		return sprintf('Order by %s %s on %s',
			$model->customer_first_name,
			$model->customer_last_name,
			$model->created_at->format('Y-m-d')
		);
	}

	public function getFields($instance)
	{
		return [
			new SelectField(array('name' => 'state_id', 'label' => 'Order State', 'options' => $this->model->state()->getRelated()->lists('name', 'id'))),
			new TextField(array('name' => 'customer_first_name', 'disabled' => true)),
			new TextField(array('name' => 'customer_last_name', 'disabled' => true)),
			new TextField(array('name' => 'customer_email', 'disabled' => true))
		];
	}
}
