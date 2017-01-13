<?php

namespace Bozboz\Ecommerce\Http\Controllers;

use App\Ecommerce\Shipping\Mailman;
use Bozboz\Ecommerce\Checkout\EmptyCartException;
use Bozboz\Ecommerce\Orders\Cart\CartStorageInterface;
use Bozboz\Ecommerce\Orders\OrderableException;
use Bozboz\Ecommerce\Products\OrderableProduct;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class CartController extends Controller
{
	protected $storage;

	public function __construct(CartStorageInterface $storage)
	{
		$this->storage = $storage;

		// $this->beforeFilter('cart-redirect', ['except' => ['index', 'add', 'addVoucher']]);
		// $this->beforeFilter('basket-timeout');
	}

	public function index(Mailman $mailman)
	{
		$cart = $this->storage->getCart();

		if ($cart) {
			$customerCountry = $cart->shippingAddress ? $cart->shippingAddress->country : 'GB';
			$deliveryMethods = $mailman->getValidDeliveryMethods($cart, $customerCountry);
		} else {
			$deliveryMethods = [];
		}

		return View::make('ecommerce::cart.cart')->with([
			'cart' => $cart,
			'deliveryMethods' => $deliveryMethods,
		]);
	}

	public function addVoucher(Request $request, $factory)
	{
		try {
			$cart = $this->storage->getCart();
			$voucherCode = $request->get('voucher_code');
			$voucher = $factory->whereCode($voucherCode)->firstOrFail();
			$cart->add($voucher);
		} catch (Exception $e) {
			return redirect()->route('cart')->withErrors($e->getErrors());
		} catch (OrderableException $e) {
			return Redirect::route('cart')->withErrors($e->getErrors());
		} catch (ModelNotFoundException $e) {
			return redirect()->route('cart')->withErrors(sprintf('Voucher code "%s" not recognised', $voucherCode));
		}
		return Redirect::back();
	}

	public function add(Request $request)
	{
		$cart = $this->storage->getOrCreateCart();

		try {
			$model = $request->get('orderable_type', OrderableProduct::class);
			$item = $cart->add(
				$model::find($request->get('orderable_id')),
				$request->get('quantity', 1)
			);
		} catch (OrderableException $e) {
			return Redirect::back()->withErrors($e->getErrors());
		}

		if ($request->has('redirect_after')) {
			$redirect = redirect($request->get('redirect_after'));
		} else {
			$redirect = redirect()->route('cart');
		}

		return $redirect->with('product_added_to_cart', $item->name);
	}

	public function remove(Request $request, $id)
	{
		$cart = $this->storage->getCart();

		if ($cart) {
			$cart->removeById($id);
		}

		return $this->redirectBack($request);
	}

	public function update(Request $request, Container $container)
	{
		$cart = $this->storage->getCartOrFail();

		if ($request->has('remove')) {
			foreach($request->get('remove') as $id) {
				$cart->removeById($id);
			}
			return $this->redirectBack($request);
		}

		if ($request->has('clear')) {
			return $this->destroy();
		}

		if ($request->has('voucher') || $request->get('voucher_code')) {
			return $this->addVoucher($request, $container->make('voucher-factory'));
		}

		try {
			$cart->updateQuantities($request->get('quantity'));
		} catch (OrderableException $e) {
			return Redirect::route('cart')->withErrors($e->getErrors());
		}

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
