<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyConstraints extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/**
		 * Deleting a state belonging to orders is restricted
		 * Deleting a user belonging to orders nullifies the user_id
		 * Deleting an address belonging to orders (shipping or billing) is restricted
		 * Deleting an order which is the parent of an order nullifies the parent_order_id
		 */
		Schema::table('orders', function(Blueprint $table)
		{
			$table->foreign('state_id')->references('id')->on('order_states')->onDelete('restrict');
		//	$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
			$table->foreign('shipping_address_id')->references('id')->on('addresses')->onDelete('restrict');
			$table->foreign('billing_address_id')->references('id')->on('addresses')->onDelete('restrict');
			$table->foreign('parent_order_id')->references('id')->on('orders')->onDelete('set null');
		});

		/**
		 * Deleting an order belong to order_items is restricted
		 */
		Schema::table('order_items', function(Blueprint $table)
		{
			$table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
		});

		/**
		 * Deleting an address or customer removes the link
		 */
		Schema::table('address_customer', function(Blueprint $table)
		{
			$table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
//			$table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
		});

		/**
		 * Deleting a category containing a product is restricted
		 * Deleting a parent product deletes the variations
		 * Deleting a shipping band containing a product nullifies the shipping_band_id
		 */
		Schema::table('products', function(Blueprint $table)
		{
			$table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
			$table->foreign('variation_of_id')->references('id')->on('products')->onDelete('cascade');
			$table->foreign('shipping_band_id')->references('id')->on('shipping_bands')->onDelete('set null');
		});

		/**
		 * Deleting a parent category deletes the children
		 */
		Schema::table('categories', function(Blueprint $table)
		{
			$table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
		});

		/**
		 * Deleting a category or product removes the link
		 */
		Schema::table('category_product', function(Blueprint $table)
		{
			$table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
		});

		/**
		 * Deleting a product removes the link to a related product
		 */
		Schema::table('related_products', function(Blueprint $table)
		{
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
			$table->foreign('related_product_id')->references('id')->on('products')->onDelete('cascade');
		});

		/**
		 * Deleting a shipping band belonging to a shipping method is restricted
		 */
		Schema::table('shipping_methods', function(Blueprint $table)
		{
			$table->foreign('shipping_band_id')->references('id')->on('shipping_bands')->onDelete('restrict');
		});

		/**
		 * Deleting a shipping method deletes the associated shipping costs
		 */
		Schema::table('shipping_costs', function(Blueprint $table)
		{
			$table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('orders', function(Blueprint $table)
		{
			$table->dropForeign('orders_state_id_foreign');
		//	$table->dropForeign('orders_user_id_foreign');
			$table->dropForeign('orders_shipping_address_id_foreign');
			$table->dropForeign('orders_billing_address_id_foreign');
			$table->dropForeign('orders_parent_order_id_foreign');
		});

		Schema::table('order_items', function(Blueprint $table)
		{
			$table->dropForeign('order_items_order_id_foreign');
		});

		Schema::table('address_customer', function(Blueprint $table)
		{
			$table->dropForeign('address_customer_address_id_foreign');
//			$table->dropForeign('address_customer_customer_id_foreign');
		});

		Schema::table('products', function(Blueprint $table)
		{
			$table->dropForeign('products_category_id_foreign');
			$table->dropForeign('products_variation_of_id_foreign');
			$table->dropForeign('products_shipping_band_id_foreign');
		});

		Schema::table('categories', function(Blueprint $table)
		{
			$table->dropForeign('categories_parent_id_foreign');
		});

		Schema::table('category_product', function(Blueprint $table)
		{
			$table->dropForeign('category_product_category_id_foreign');
			$table->dropForeign('category_product_product_id_foreign');
		});

		Schema::table('related_products', function(Blueprint $table)
		{
			$table->dropForeign('related_products_product_id_foreign');
			$table->dropForeign('related_products_related_product_id_foreign');
		});

		Schema::table('shipping_methods', function(Blueprint $table)
		{
			$table->dropForeign('shipping_methods_shipping_band_id_foreign');
		});

		Schema::table('shipping_costs', function(Blueprint $table)
		{
			$table->dropForeign('shipping_costs_shipping_method_id_foreign');
		});
	}

}
