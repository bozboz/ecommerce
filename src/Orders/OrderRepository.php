<?php

namespace Bozboz\Ecommerce\Orders;

use Bozboz\Ecommerce\Orders\Order;
use Session;

class OrderRepository
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function lookupOrder()
    {
        return $this->order->find(Session::get('order'));
    }

    public function hasOrder()
    {
        return Session::has('order');
    }
}