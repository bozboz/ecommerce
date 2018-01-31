# Ecommerce package

## Installation

1. Require the package in Composer, by running `composer require bozboz/ecommerce`
2. Add  to the providers array in config/app.php
        
        Bozboz\Ecommerce\Providers\EcommerceServiceProvider::class,

3. Run `php artisan vendor:publish && php artisan migrate` 
7. Edit `config/ecommerce.php`: