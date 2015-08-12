<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductProductAttributeOptionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_product_attribute_option', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->integer('product_id')->unsigned();
			$table->integer('product_attribute_option_id')->unsigned();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_product_attribute_option');
	}

}
