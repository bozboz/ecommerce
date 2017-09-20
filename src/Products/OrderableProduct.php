<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Ecommerce\Orders\Exception;
use Bozboz\Ecommerce\Orders\Item;
use Bozboz\Ecommerce\Orders\Order;
use Bozboz\Ecommerce\Orders\Orderable;
use Bozboz\Ecommerce\Orders\OrderableException;
use Bozboz\Ecommerce\Products\Pricing\PriceTrait;
use Bozboz\Ecommerce\Products\Product;
use Bozboz\Ecommerce\Shipping\Shippable;
use Bozboz\Ecommerce\Shipping\ShippableTrait;
use Bozboz\Users\User;
use Breadcrumbs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as Validator;

abstract class OrderableProduct extends Product implements Orderable, Shippable
{
    use ShippableTrait, PriceTrait;

    public function items()
    {
        return $this->morphMany(Item::class, 'orderable');
    }

    public function canAdjustQuantity()
    {
        return true;
    }

    public function canDelete()
    {
        return true;
    }

    public function validate($quantity, Item $item, Order $order)
    {
        $validation = Validator::make(
            array('stock' => $quantity),
            array('stock' => 'numeric|max:' . ($this->stock_level + $item->quantity)),
            array('max' => 'Sorry. Some or all of the requested items are out of stock')
        );

        if ($validation->fails()) {
            if ($validation->errors()->first('stock') && $this->stock_level) {
                $item->quantity += $this->stock_level;
                $item->total_weight = $this->calculateWeight($item->quantity);
                $item->calculateNet($this, $order);
                $item->calculateGross();
                $item->save();

                $this->stock_level = 0;
                $this->save();
            }
            throw new OrderableException($validation);
        }
    }

    public function calculatePrice($quantity, Order $order)
    {
        return $quantity * $this->price * 100 / (1 + $this->tax_rate);
    }

    public function calculateAmountToRefund(Item $item, $quantity)
    {
        return $this->attributes[$this->getRawPriceField()] * $quantity;
    }

    public function purchased($quantity)
    {
        if ($this->stock_level > $quantity) {
            $this->decrement('stock_level', $quantity);
        } else {
            $this->stock_level = 0;
            $this->save();
        }

        if ($this->stock_level === 0) {
            event(new Events\StockLevelDepleted($this));
        }
    }
}
