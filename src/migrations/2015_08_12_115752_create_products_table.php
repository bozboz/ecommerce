<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name');
			$table->string('slug');
			$table->string('description');
			$table->integer('category_id')->unsigned()->nullable();
			$table->integer('price_pence');
			$table->integer('variation_of_id')->unsigned()->nullable();
			$table->integer('stock_level');
			$table->integer('weight');
			$table->integer('nominal_code');
			$table->string('sku', 50);
			$table->boolean('status');
			$table->boolean('tax_exempt');
			$table->integer('shipping_band_id')->unsigned()->nullable();
			$table->timestamps();
			$table->softDeletes();

			$table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
			$table->foreign('variation_of_id')->references('id')->on('products')->onDelete('cascade');
			$table->foreign('shipping_band_id')->references('id')->on('shipping_bands')->onDelete('restrict');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products');
	}

}
