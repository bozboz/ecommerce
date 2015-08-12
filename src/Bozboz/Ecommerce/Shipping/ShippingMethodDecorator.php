<?php namespace Bozboz\Ecommerce\Shipping;

use Bozboz\Admin\Decorators\ModelAdminDecorator;
use Bozboz\Admin\Fields\BelongsToField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\StandardModelAdminDecorator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Str;

class ShippingMethodDecorator extends ModelAdminDecorator
{
	private $shippingCost;

	public function __construct(ShippingMethod $model, ShippingCostDecorator $cost)
	{
		$this->shippingCost = $cost;
		parent::__construct($model);
	}

	public function getFields($instance)
	{
		return [
			new TextField('name'),
			new BelongsToField(new StandardModelAdminDecorator, $instance->band()),
			new CheckboxField('is_default')
		];
	}

	public function getColumns($instance)
	{
		return [
			'Name' => $instance->name . ($instance->is_default ? ' <strong>(x)</strong>' : ''),
			'Band' => $instance->band ? $instance->band->name : '-',
			'Costs' => $this->getCosts($instance)
		];
	}

	private function getCosts($instance)
	{
		$costsList = implode(PHP_EOL, $instance->costs->map(function($cost) {
			return '<li>' . HTML::linkRoute(
				'admin.shipping.costs.edit',
				$this->shippingCost->getLabel($cost),
				[ $cost->id ]
			) . '</li>';
		})->all());

		return "<ul class=\"secret-list\">{$costsList}</ul>";
	}

	public function getLabel($instance)
	{
		return $instance->name;
	}

	public function modifyListingQuery(Builder $query)
	{
		$query
			->with('costs')
			->orderBy('shipping_band_id')
			->orderBy('is_default', 'DESC')
			->orderBy('name', 'ASC');
	}
}
