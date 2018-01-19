<?php

namespace Bozboz\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Bozboz\Ecommerce\Orders\Orderable;
use Illuminate\Support\Facades\Redirect;
use Bozboz\Ecommerce\Orders\OrderableFactory;
use Illuminate\Contracts\Container\Container;
use Bozboz\Ecommerce\Orders\OrderableException;
use Bozboz\Ecommerce\Checkout\EmptyCartException;
use Bozboz\Ecommerce\Orders\Cart\CartMissingException;
use Bozboz\Ecommerce\Orders\Cart\CartStorageInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
	protected $storage;

	public function __construct(CartStorageInterface $storage)
	{
		$this->storage = $storage;

		// $this->beforeFilter('cart-redirect', ['except' => ['index', 'add', 'addVoucher']]);
		// $this->beforeFilter('basket-timeout');
	}

	public function index()
	{
		$cart = $this->storage->getCart();

		return View::make('orders::cart.cart')->with(compact('cart'));
	}

	public function add(Request $request)
	{
        $cart = $this->storage->getOrCreateCart();

        DB::beginTransaction();

        try {
            if (count($request->get('products'))) {
                $itemName = collect($request->get('products'))->map(function($item) use ($cart) {
                    if ($item['quantity'] < 1) return false;

                    return $this->addSingleItem(
                        $cart,
                        app($item['orderable_factory']),
                        $item['orderable'],
                        $item['quantity'] ?: 1
                    )->name;
                })->filter()->implode(', ');
            } else {
                $itemName = $this->addSingleItem(
                    $cart, app($request->get('orderable_factory')),
                    $request->get('orderable'),
                    $request->get('quantity', 1)
                )->name;
            }

        } catch (OrderableException $e) {
            return Redirect::back()->withErrors($e->getErrors());
        }

        DB::commit();

        if ($request->has('redirect_after')) {
            $redirect = redirect($request->get('redirect_after'));
        } else {
            $redirect = redirect()->route('cart');
        }

        return $redirect->with('product_added_to_cart', $itemName);
	}

    private function addSingleItem($cart, OrderableFactory $factory, $orderable, $quantity)
    {
        return $cart->add(
            $factory->find($orderable),
            $quantity
        );
    }

	public function remove(Request $request, $id)
	{
		DB::beginTransaction();
		$cart = $this->storage->getCart();

		if ($cart) {
			$cart->removeById($id);
		}
		DB::commit();

		return $this->redirectBack($request);
	}

	public function update(Request $request, Container $container)
	{
		DB::beginTransaction();
		try {
			$cart = $this->storage->getCartOrFail();
		} catch (CartMissingException $e) {
			return redirect()->route('cart');
		}

		if ($request->has('remove')) {
			foreach($request->get('remove') as $id) {
				$cart->removeById($id);
			}
			DB::commit();
			return $this->redirectBack($request);
		}

		if ($request->has('clear')) {
			DB::commit();
			return $this->destroy();
		}

		try {
			$cart->updateQuantities($request->get('quantity'));
		} catch (OrderableException $e) {
			DB::commit();
			return Redirect::route('cart')->withErrors($e->getErrors());
		}

		DB::commit();
		return $this->redirectBack($request);
	}

	public function destroy()
	{
		$this->storage->getCartOrFail()->clearItems();

		return Redirect::route('cart');
	}

	protected function redirectBack($request)
	{
		if ($request->header('referer')) {
			return Redirect::back();
		} else {
			return Redirect::route('cart');
		}
	}
}
