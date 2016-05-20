<?php

namespace Bozboz\Ecommerce\Products;

use Bozboz\Ecommerce\Orders\Exception;
use Bozboz\Ecommerce\Orders\Item;
use Bozboz\Ecommerce\Orders\Order;
use Bozboz\Ecommerce\Orders\Orderable;
use Bozboz\Ecommerce\Orders\OrderableException;
use Bozboz\Ecommerce\Products\Product;
use Bozboz\Ecommerce\Shipping\Shippable;
use Bozboz\Ecommerce\Shipping\ShippableTrait;
use Bozboz\Users\User;
use Breadcrumbs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as Validator;

class OrderableProduct extends Product implements Orderable, Shippable
{
    use ShippableTrait;

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
            array('stock' => 'numeric|max:' . $this->stock_level),
            array('max' => 'Sorry. Some or all of the requested items are out of stock')
        );

        if ($validation->fails()) {
            if ($validation->errors()->first('stock') && $this->stock_level) {
                $item->quantity = $this->stock_level;
                $item->total_weight = $this->calculateWeight($item->quantity);
                $item->calculateNet($this, $order);
                $item->calculateGross();
                $item->save();
            }
            throw new OrderableException($validation);
        }
    }

    public function calculatePrice($quantity, Order $order)
    {
        return $quantity * $this->price * 100 / (1 + $this->tax_rate);
    }

    public function calculateWeight($quantity)
    {
        return $this->weight * $quantity;
    }

    public function calculateAmountToRefund(Item $item, $quantity)
    {
        return $item->price_pence_ex_vat * $quantity;
    }

    public function isTaxable()
    {
        return ! $this->tax_exempt;
    }

    /**
     * Returns an array with properties which must be indexed
     *
     * @return array
     */
    public function getSearchableBody()
    {
        return [
            'title' => $this->name,
            'url' => route('product-detail', $this->slug),
            'content' => $this->description,
            'image' => $this->image(),
            'breadcrumbs' => Breadcrumbs::render('product-detail', $this->name),
            'membership_types' => [],

            'price' => $this->getPriceForUser(),
            'data' => $this->toArray(),
        ];
    }
}
