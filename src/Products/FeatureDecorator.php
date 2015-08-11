<?php

namespace Bozboz\Ecommerce\Products;

use Illuminate\Support\Facades\HTML;
use Bozboz\Admin\Decorators\ModelAdminDecorator;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Admin\Fields\BelongsToManyField;

class FeatureDecorator extends ModelAdminDecorator
{
	private $productDecorator;

	public function __construct(Feature $model, ProductDecorator $productDecorator)
	{
		parent::__construct($model);
		$this->productDecorator = $productDecorator;
	}

	public function getListingModels()
	{
		return $this->model->with(['products' => function($q) {
			$q->select('products.id');
		}])->get();
	}

	public function getColumns($feature)
	{
		return array(
			'Name' => $this->getLabel($feature),
			'Page' => HTML::linkRoute('shop.featured.feature', null, $feature->slug),
			'Products' => $feature->products->count()
		);
	}

	public function getLabel($model)
	{
		return $model->name;
	}

	public function getFields($instance)
	{
		return array(
			new TextField(array('name' => 'name')),
			new TextField(array('name' => 'slug')),
			new BelongsToManyField(
				$this->productDecorator, $instance->products(), ['label' => 'Products'], function($query) {
					$query->whereNull('variation_of_id')->orderBy('name');
				}
			),
		);
	}

	public function getSyncRelations()
	{
		return ['products'];
	}
}
