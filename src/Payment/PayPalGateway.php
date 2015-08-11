<?php namespace Bozboz\Ecommerce\Payment;

use Omnipay\PayPal\ExpressGateway;
use Bozboz\Ecommerce\Order\Order;
use Illuminate\Routing\UrlGenerator;

class PayPalGateway implements PaymentGateway
{
	private $gateway;
	private $url;

	public function __construct(ExpressGateway $gateway, UrlGenerator $url)
	{
		$this->gateway = $gateway;
		$this->url = $url;
	}

	public function purchase($data, Order $order)
	{
		$request = $this->gateway->purchase($this->orderDetails($order));
		$request->setItems($this->orderToArray($order));

		return $request->send();
	}

	public function completePurchase(Order $order)
	{
		$request = $this->gateway->completePurchase($this->orderDetails($order));
		$request->setItems($this->orderToArray($order));

		return $request->send();
	}

	private function orderDetails(Order $order)
	{
		return [
			'amount' => number_format($order->totalprice() / 100, 2),
			'returnurl' => $this->url->route('checkout.callback.completed'),
			'cancelurl' => $this->url->route('checkout.callback.cancel'),
			'currency' => 'gbp',
			'transactionid' => $order->getKey(),
		];
	}

	private function orderToArray($order)
	{
		$orderItems = [];
		foreach ($order->items as $orderItem) {
			$orderItems[] = [
				'name' => $orderItem->name,
				'quantity' => $orderItem->quantity,
				'price' => number_format($orderItem->price / 100, 2)
			];
		}

		return $orderItems;
	}
}
