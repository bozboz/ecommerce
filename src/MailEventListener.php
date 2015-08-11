<?php namespace Bozboz\Ecommerce;

use Illuminate\Mail\Mailer;
use Illuminate\Config\Repository as Config;
use Bozboz\Library\Downloads\Download;

class MailEventListener
{
	private $mailer, $config;

	public function __construct(Mailer $mailer, Config $config)
	{
		$this->mailer = $mailer;
		$this->config = $config;
	}

	public function subscribe($events)
	{
		$events->listen('order.completed', __CLASS__ . '@onOrderCompleted');
	}

	public function onOrderCompleted(Order\Order $order)
	{
		$data = $order->toArray();
		$data['lineItems'] = $order->items;
		$data['downloads'] = Download::where('order_id', $data['id'])->lists('hash', 'media_id');
		$data['orderTotal'] = $order->totalPrice();

		$this->mailer->send('emails.orders.confirmation', $data, function($message) use ($order)
		{
			$message->to($order->customer_email);
			if ($this->config->get('app.order_cc_email_address')) {
				$message->bcc($this->config->get('app.order_cc_email_address'));
			}
			$message->subject(sprintf('%s - Your Order', $this->config->get('app.client_name')));
		});
	}
}
