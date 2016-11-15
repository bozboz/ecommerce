<?php

/**
 * Admin routes
 */
Route::group(['middleware' => 'web', 'prefix' => 'admin', 'namespace' => 'Bozboz\Ecommerce'], function()
{
	Route::group(['namespace' => 'Orders\Http\Controllers\Admin'], function()
	{
		/* Orders */
		Route::resource('orders', 'OrderController', ['except' => ['show']]);
		Route::post('orders/{id}/invoice', [
			'uses' => 'OrderController@downloadInvoice',
			'as' => 'admin.orders.invoice'
		]);
		Route::post('orders/bulk-edit', [
			'uses' => 'OrderController@bulkEdit'
		]);
		Route::post('orders/bulk-update', [
			'uses' => 'OrderController@bulkUpdate'
		]);
		Route::post('orders/{id}/refund', [
			'uses' => 'OrderController@refund',
			'as' => 'admin.orders.refund',
			'before' => 'csrf'
		]);
		Route::get('orders/csv', [
			'uses' => 'OrderController@downloadCsv'
		]);
		Route::post('orders/transition/{id}/{transition}', [
			'uses' => 'OrderController@transitionState',
			'as' => 'admin.orders.transition-state'
		]);

		/* Customers */
		Route::resource('customers', 'CustomerController', ['except' => 'show']);
		Route::get('customers/{customer}/address/create', [
			'uses' => 'AddressController@createForCustomer',
			'as' => 'admin.customer.address.create'
		]);
		Route::post('customers/address/store', [
			'uses' => 'AddressController@store',
			'as' => 'admin.customer.address.store'
		]);
		Route::put('customers/address/{address}', [
			'uses' => 'AddressController@updateForCustomer',
			'as' => 'admin.customer.address.update'
		]);
		Route::delete('customers/address/{address}', [
			'uses' => 'AddressController@destroyForCustomer',
			'as' => 'admin.customer.address.destroy'
		]);
	});

	Route::group(['namespace' => 'Shipping\Http\Controllers\Admin'], function()
	{
		/* Shipping */
		Route::resource('shipping', 'ShippingMethodController', ['except' => 'show']);
		Route::resource('shipping/bands', 'ShippingBandController', ['except' => 'show']);
		Route::resource('shipping/costs', 'ShippingCostController', ['except' => 'show']);
		Route::get('shipping/costs/create/{method}', 'ShippingCostController@createForMethod');
	});

});

/**
 * Cart
 */
if (config('ecommerce.cart.route')) {
	Route::group(['middleware' => 'web', 'prefix' => config('ecommerce.cart.route'), 'namespace' => 'Bozboz\Ecommerce\Http\Controllers'], function()
	{
		Route::get('/', [
			'as' => 'cart',
			'uses' => 'CartController@index'
		]);

		Route::post('/', [
			'uses' => 'CartController@update',
			'as' => 'cart.update'
		]);

		Route::post('items', [
			'as' => 'cart.add',
			'uses' => 'CartController@add'
		]);

		Route::post('voucher', [
			'as' => 'cart.add-voucher',
			'uses' => 'CartController@addVoucher'
		]);

		Route::delete('/', [
			'as' => 'cart.clear',
			'uses' => 'CartController@destroy'
		]);

		Route::delete('items/{id}', [
			'as' => 'cart.remove-item',
			'uses' => 'CartController@remove'
		]);

		Route::get('items/remove/{id}/{sessionId}', [
			'as' => 'cart.remove-item',
			'uses' => 'CartController@remove',
			'before' => 'sessionProtect'
		]);
	});
}
