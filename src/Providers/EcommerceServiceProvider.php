<?php

namespace Bozboz\Ecommerce\Providers;

use Bozboz\Ecommerce\Cart\Cart;
use Bozboz\Ecommerce\Cart\CartObserver;
use Bozboz\Ecommerce\Checkout\Checkout;
use Bozboz\Ecommerce\Payment\IFrameSagePayGateway;
use Bozboz\Ecommerce\Payment\PayPalGateway;
use Bozboz\Ecommerce\Payment\SagePayGateway;
use Illuminate\Support\ServiceProvider;
use Omnipay\Omnipay;

class EcommerceServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->register('Bozboz\Ecommerce\Products\Providers\ProductServiceProvider');
		$this->app->register('Bozboz\Ecommerce\Orders\Providers\OrderServiceProvider');
		$this->app->register('Bozboz\Ecommerce\Checkout\Providers\CheckoutServiceProvider');
		// $this->app->register(PaymentServiceProvider::class);
	}


	public function boot()
	{
		require __DIR__ . '/../Http/routes.php';

		$this->buildAdminMenu();
	}

	private function buildAdminMenu()
	{
		$event = $this->app['events'];

		$event->listen('admin.renderMenu', function($menu)
		{
			$url = $this->app['url'];
			$lang = $this->app['translator'];

			$menu[$lang->get('ecommerce::ecommerce.menu_name')] = [
				'Products' => $url->route('admin.products.index'),
				'Categories' => $url->route('admin.categories.index'),
				'Orders' => $url->route('admin.orders.index'),
				// 'Shipping' => $url->route('admin.shipping.index'),
				'Customers' => $url->route('admin.customers.index'),
			];
		});
	}
}
