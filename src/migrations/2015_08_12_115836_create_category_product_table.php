<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoryProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('category_product', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->integer('category_id')->unsigned()->index();
			$table->integer('product_id')->unsigned()->index();
			$table->timestamps();
			
			$table->primary(['category_id', 'product_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('category_product');
	}

}