<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRelatedProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('related_products', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->integer('product_id')->unsigned()->index();
			$table->integer('related_product_id')->unsigned()->index();
			$table->timestamps();

			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
			$table->foreign('related_product_id')->references('id')->on('products')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('related_products');
	}

}
