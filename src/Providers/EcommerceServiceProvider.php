<?php namespace Bozboz\Ecommerce\Providers;

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
		$this->app->bind('cart', 'Bozboz\Ecommerce\Cart\Cart');

		$this->app->bind(
			'Bozboz\Ecommerce\Cart\CartStorageInterface',
			'Bozboz\Ecommerce\Cart\SessionStorage'
		);

		$this->registerPaymentGateways();
	}

	private function registerPaymentGateways()
	{
		$this->app->bind('Bozboz\Ecommerce\Payment\PayPalGateway', function($app)
		{
			$gateway = Omnipay::create('PayPal_Express');

			$key = $app['config']->get('payment.paypal.sandbox_mode_enabled') ? 'sandbox' : 'live';

			$gateway->setUsername($app['config']->get("payment.paypal.{$key}_username"));
			$gateway->setPassword($app['config']->get("payment.paypal.{$key}_password"));
			$gateway->setSignature($app['config']->get("payment.paypal.{$key}_signature"));
			$gateway->setBrandName($app['config']->get("payment.paypal.brand_name"));
			$gateway->setTestMode($app['config']->get("payment.paypal.sandbox_mode_enabled"));

			return new PayPalGateway($gateway, $app['url']);
		});

		$this->app->bind('Bozboz\Ecommerce\Payment\SagePayGateway', function($app)
		{
			$gateway = Omnipay::create('SagePay_Direct');

			$gateway->setSimulatorMode($app['config']->get('payment.sagepay.simulatorMode'));
			$gateway->setTestMode($app['config']->get('payment.sagepay.testMode'));
			$gateway->setVendor($app['config']->get('payment.sagepay.vendor'));

			return new SagePayGateway($gateway, $app['validator']);
		});

		$this->app->bind('Bozboz\Ecommerce\Payment\IFrameSagePayGateway', function($app)
		{
			$gateways = [
				Omnipay::create('SagePay_Server'),
				Omnipay::create('SagePay_Direct')
			];

			$simulatorMode = $app['config']->get('payment.sagepay.simulatorMode');
			$testMode = $app['config']->get('payment.sagepay.testMode');
			$vendor = $app['config']->get('payment.sagepay.vendor');

			foreach($gateways as $gateway) {
				$gateway->setSimulatorMode($simulatorMode);
				$gateway->setTestMode($testMode);
				$gateway->setVendor($vendor);
			}

			list($server, $direct) = $gateways;

			return new IFrameSagePayGateway($server, $direct, $app['url'], $app['request']);
		});

		$this->app->bind('Bozboz\Ecommerce\Payment\ExternalGateway', function($app)
		{
			if ($app['config']->get('payment.test_payments')) {
				return $app['Bozboz\Ecommerce\Payment\Test\TestIFrameGateway'];
			} else {
				return $app['Bozboz\Ecommerce\Payment\IFrameSagePayGateway'];
			}
		});

		$this->app->bind('Bozboz\Ecommerce\Payment\CreditCardGateway', function($app)
		{
			if ($app['config']->get('payment.test_payments')) {
				return $app['Bozboz\Ecommerce\Payment\Test\TestCardGateway'];
			} else {
				return $app['Bozboz\Ecommerce\Payment\SagePayGateway'];
			}
		});
	}

	public function boot()
	{
		require __DIR__ . '/../helpers.php';
		require __DIR__ . '/../Http/routes.php';

		$this->package('bozboz/checkout');

		$this->registerEvents();
	}

	private function registerEvents()
	{
		$event = $this->app['events'];

		$event->listen('admin.renderMenu', function($menu)
		{
			$url = $this->app['url'];
			$menu['E-commerce'] = [
				'Products' => $url->route('admin.products.index'),
				'Categories' => $url->route('admin.categories.index'),
				'Orders' => $url->route('admin.orders.index'),
				'Shipping' => $url->route('admin.shipping.index'),
				'Customers' => $url->route('admin.customers.index'),
			];
		});

		$event->subscribe('Bozboz\Ecommerce\MailEventListener');

		$event->listen(
			'order.completed',
			'Bozboz\Ecommerce\Address\OrderCompletedEvent'
		);

		$event->listen(
			'order.shipping-country-changed',
			'Bozboz\Ecommerce\Shipping\AddressCountryChangedEvent'
		);

		$event->listen(
			'item.purchased: Bozboz\Ecommerce\Products\OrderableProduct',
			'Bozboz\Ecommerce\Products\ProductPurchasedEvent'
		);
	}
}
