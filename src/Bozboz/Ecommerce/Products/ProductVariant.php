<?php

namespace Bozboz\Ecommerce\Products;

class ProductVariant extends Product
{
	protected $table = 'products';
	protected $fieldsFromParent = ['name', 'slug', 'price', 'description', 'status', 'manufacturer_id'];

	public static function boot()
	{
		parent::boot();

		static::creating([new static, 'copyAttributesFromParent']);
	}

	public function variantLabel()
	{
		$attributeOptions = $this->attributeOptions()->lists('value');
		$stockAvailability = '';

		if (!$this->isAvailable()) {
			$stockAvailability = '&nbsp;&nbsp;(OUT OF STOCK)';
		} elseif ($this->stock_level < 10) {
			$stockAvailability = '&nbsp;&nbsp;(Less than 10 in stock)';
		}

		return implode(' ', $attributeOptions) . ' - ' . $this->price . $stockAvailability;
	}

	public function getValidator()
	{
		return new ProductVariantValidator;
	}

	public function copyAttributesFromParent($model)
	{
		foreach($this->fieldsFromParent as $field) {
			if (empty($model->$field)) {
				$model->$field = $model->variationOf->$field;
			}
		}
	}

}
