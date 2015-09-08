<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Models\Base;
use Bozboz\Admin\Traits\DynamicSlugTrait;
use Bozboz\Ecommerce\Products\BrandValidator;
use Bozboz\MediaLibrary\Models\Media;

class Brand extends Base
{
	use DynamicSlugTrait;

	protected $table = 'brands';

	protected $fillable = ['name', 'logo_id'];

	public function getSlugSourceField()
	{
		return 'name';
	}

	public function getValidator()
	{
		return new BrandValidator();
	}

	public function products()
	{
		return $this->hasMany('Bozboz\Ecommerce\Products\Product');
	}

	public function logo()
	{
		return Media::forModel($this, 'logo_id');
	}
}
