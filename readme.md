# Ecommerce package

## Installation

1. Require the package in Composer, by running `composer require bozboz/ecommerce`
2. Add  to the providers array in config/app.php
        
        Bozboz\Ecommerce\Providers\EcommerceServiceProvider::class,

3. Run `php artisan vendor:publish && php artisan migrate` 
4. Edit `config/ecommerce.php`

## Usage

### CartController

The cart route is set in config under 'ecommerce.cart.route'. This will set up the following routes, prefixed with the configured cart route:

```
+--------+--------------------------------+------------------------+
| Method | URL                            | Use                    |
+--------+--------------------------------+------------------------+
| GET    | /                              | view cart              |
| POST   | /                              | update cart quantities |
| DELETE | /                              | clear cart             |
| POST   | /items                         | add item               |
| DELETE | /items/{id}                    | delete item via form   |
| GET    | /items/remove/{id}/{sessionId} | delete item via link   |
+--------+--------------------------------+------------------------+
```

For more information regarding cart usage see [bozboz/orders](http://gitlab.lab/laravel-packages/orders)

__\#TODO:__ The CartController should probably be in bozboz/orders since that's where all the models are.

### Other Packages

- [bozboz/orders](http://gitlab.lab/laravel-packages/orders)
- [bozboz/payment](http://gitlab.lab/laravel-packages/payment)
- [bozboz/products](http://gitlab.lab/laravel-packages/products)
- [bozboz/shipping](http://gitlab.lab/laravel-packages/shipping)
- [bozboz/vouchers](http://gitlab.lab/laravel-packages/vouchers)

The idea behind having these all split up in to separate packages is that they could be used in isolation though that idea has not yet been properly explored and there's a good chance that they wouldn't function in any useful/meaningful way on their own.

Each package has its own readme so follow the links above to read up on how to use them.




