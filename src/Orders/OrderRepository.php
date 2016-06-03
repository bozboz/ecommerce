<?php

namespace Bozboz\Ecommerce\Orders;

use Bozboz\Ecommerce\Checkout\Checkoutable;
use Bozboz\Ecommerce\Orders\Order;
use Session;

class OrderRepository implements Checkoutable
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getCheckoutable()
    {
        return $this->order->find(Session::get('order'));
    }

    public function hasCheckoutable()
    {
        return Session::has('order');
    }

    public function getCompletedScreen($order)
    {
        return $order->getCheckoutProgress();
    }

    public function markScreenAsComplete($order, $screenAlias)
    {
        $order->updateCheckoutProgress($screenAlias);
    }
}