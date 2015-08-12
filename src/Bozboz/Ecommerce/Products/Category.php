<?php namespace Bozboz\Ecommerce\Products;

use Bozboz\Admin\Models\Base;
use Bozboz\MediaLibrary\Models\MediableTrait;

class Category extends Base
{
	use MediableTrait;
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
		return $query->where('parent_id', 0);
	}

	public function children()
	{
		return $this->hasMany(get_class($this), 'parent_id');
	}

	public function parent()
	{
		return $this->belongsTo(get_class($this), 'parent_id');
	}

}
