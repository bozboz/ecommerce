<?php namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Models\BaseInterface;
use Bozboz\MediaLibrary\Models\MediableTrait;
use Bozboz\Admin\Traits\SanitisesInputTrait;

class Category extends \Baum\Node implements BaseInterface
{
	use MediableTrait;

	use SanitisesInputTrait;

	protected $nullable = ['parent_id'];

	protected $table = 'categories';

	protected $fillable = array('name', 'slug', 'description', 'parent_id');

	public function products()
	{
		return $this->belongsToMany('Bozboz\Ecommerce\Products\Product');
	}

	public function getValidator()
	{
		return new CategoryValidator;
	}

	public function scopeTopLevel($query)
	{
		return $query->whereNull('parent_id');
	}

	public function sanitiseInput($input) {
		return $input;
	}
}
