<?php namespace Bozboz\Ecommerce\Fields;

use View;
use Bozboz\Admin\Fields\Field;
use Bozboz\Users\Membership\MembershipType;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ViewErrorBag;

class PricesField extends Field
{
	protected $relation;

	public function __construct(Relation $relation, $attributes = [])
	{
		$this->relation = $relation;
		parent::__construct($attributes);
	}

	public function defaultAttributes()
	{
		return [
			'name' => 'prices_data',
			'label' => 'Prices'
		];
	}

	public function getInput()
	{
		$membershipTypes = MembershipType::lists('name', 'id');

		$currentPrices = $this->relation->get();

		$prices = $currentPrices->lists('price', 'membership_type_id');
		$priceIds = $currentPrices->lists('id', 'membership_type_id');

		return View::make('resources.admin.prices')->with([
			'name' => $this->name,
			'membershipTypes' => $membershipTypes,
			'prices' => $prices,
			'priceIds' => $priceIds
		]);
	}

	public function getErrors(ViewErrorBag $errors)
	{
		if ($this->name && $errors->first($this->name)) {
			return '<p><strong>' . $errors->first($this->name) . '</strong></p>';
		}
	}
}
