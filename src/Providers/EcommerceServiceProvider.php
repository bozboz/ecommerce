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
    protected $listen = [
        'Bozboz\Ecommerce\Orders\Events\ItemOrdered' => [
            'Bozboz\Ecommerce\Products\Listeners\Purchased',
        ],
    ];

    public function register()
    {
        $this->app->register('Bozboz\Ecommerce\Products\Providers\ProductServiceProvider');
        $this->app->register('Bozboz\Ecommerce\Orders\Providers\OrderServiceProvider');
        $this->app->register('Bozboz\Ecommerce\Checkout\Providers\CheckoutServiceProvider');
        $this->app->register('Bozboz\Ecommerce\Shipping\Providers\ShippingServiceProvider');
        $this->app->register('Bozboz\Ecommerce\Payment\Providers\PaymentServiceProvider');

        $this->app->bind(
            \Bozboz\Ecommerce\Products\ProductInterface::class,
            \Bozboz\Ecommerce\Products\OrderableProduct::class
        );

        $this->app->bind(
            \Bozboz\Ecommerce\Orders\Cart\CartStorageInterface::class,
            \Bozboz\Ecommerce\Orders\Cart\SessionStorage::class
        );

        $this->app->bind('cart', Bozboz\Ecommerce\Orders\Cart\Cart::class);
    }


    public function boot()
    {
        $packageRoot = __DIR__ . "/../..";

        require "{$packageRoot}/helpers.php";

        if (! $this->app->routesAreCached()) {
            require "{$packageRoot}/src/Http/routes.php";
        }

        $this->loadViewsFrom("{$packageRoot}/resources/views", 'ecommerce');

        $this->loadTranslationsFrom("{$packageRoot}/resources/lang", 'ecommerce');

        $this->publishes([
            "{$packageRoot}/resources/views" => base_path('resources/views/vendor/ecommerce'),
            "{$packageRoot}/resources/lang" => base_path('resources/lang/vendor/ecommerce'),
            "{$packageRoot}/config/ecommerce.php" => config_path('ecommerce.php'),
        ]);

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
                'Orders' => $url->route('admin.orders.index'),
                'Shipping' => $url->route('admin.shipping.index'),
                'Customers' => $url->route('admin.customers.index'),
            ];
        });
    }
}
