<?php

Route::group(['prefix' => 'admin', 'namespace' => 'Bozboz\Ecommerce\Http\Controllers\Admin'], function()
{
	/* Orders */
	Route::resource('orders', 'OrderController', ['except' => ['show']]);
	Route::post('orders/{id}/invoice', [
		'uses' => 'OrderController@downloadInvoice',
		'as' => 'admin.orders.invoice'
	]);
	Route::post('orders/{id}/refund', [
		'uses' => 'OrderController@refund',
		'as' => 'admin.orders.refund',
		'before' => 'csrf'
	]);
	Route::post('orders/csv', [
		'uses' => 'OrderController@downloadCsv'
	]);

	/* Products */
	Route::resource('products', 'ProductController', ['except' => 'show']);

	/* Categories */
	Route::resource('categories', 'CategoryController', ['except' => 'show']);

	/* Customers */
	Route::resource('customers', 'CustomerController', ['except' => 'show']);
	Route::put('customers/{customer}/address/{address}', [
		'uses' => 'CustomerController@updateAddress',
		'as' => 'admin.customer.address.update'
	]);

	/* Shipping */
	Route::resource('shipping', 'ShippingMethodController', ['except' => 'show']);
	Route::resource('shipping/bands', 'ShippingBandController', ['except' => 'show']);
	Route::resource('shipping/costs', 'ShippingCostController', ['except' => 'show']);
	Route::get('shipping/costs/create/{method}', 'ShippingCostController@createForMethod');

});
