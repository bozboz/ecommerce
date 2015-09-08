<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Ecommerce\Products\Brand;
use Bozboz\Admin\Decorators\ModelAdminDecorator;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\URLField;
use Bozboz\MediaLibrary\Fields\MediaBrowser;

class BrandDecorator extends ModelAdminDecorator
{
	public function __construct(Brand $brand)
	{
		parent::__construct($brand);
	}

	public function getColumns($brand)
	{
		return [
			'Name' => $this->getLabel($brand),
		];
	}

	public function getLabel($brand)
	{
		return $brand->name;
	}

	public function getFields($brand)
	{
		return [
			new TextField('name'),
			new URLField('slug', ['route' => 'brands.detail']),
			new MediaBrowser($brand->logo())
		];
	}
}
