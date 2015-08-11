<?php namespace Bozboz\Ecommerce\Fields;

use Bozboz\Admin\Fields\TextField;
use Form;

class PriceField extends TextField
{
	public function getInput()
	{
		$input = parent::getInput();
		$tax = Form::hidden('price_includes_tax', 0) . Form::label('price_includes_tax', 'Includes tax', [
			'style' => 'display: inline-block; padding-right: 8px'
		])
		 . Form::checkbox('price_includes_tax');

		return <<<HTML
			<div class="input-group">
				<span class="input-group-addon">&pound;</span>
				{$input}
				<span class="input-group-addon">
					{$tax}
				</span>
			</div>
HTML;
	}
}
