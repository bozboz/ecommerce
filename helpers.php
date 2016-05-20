<?php

if (!function_exists('format_money')) {
	function format_money($amount)
	{
		setlocale(LC_MONETARY, 'en_GB.UTF-8');
		return money_format('%.2n', $amount/100);
	}
}
