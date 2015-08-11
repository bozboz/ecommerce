<?php

if (!function_exists('format_money')) {
	function format_money($amount)
	{
		$symbol = $amount < 0 ? '-' : '';
		$amount *= $amount < 0 ? -1 : 1;

		return $symbol . '£' . number_format($amount / 100, 2);
	}
}

if (!function_exists('generate_random_alphanumeric_string')) {
	function generate_random_alphanumeric_string($stringLength = 8)
	{
		$string = '';
		//Alphanumeric characters, with similar looking characters excluded
		$characters = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
		$characterCount = strlen($characters);
		for ($i = 0; $i < $stringLength; $i++) {
			$stringIndex  = mt_rand(0, $characterCount - 1);
			$string .= $characters[$stringIndex];
		}

		return $string;
	}
}

if (!function_exists('alphabet')) {
	function alphabet($index) {
		return substr('abcdefghijklmnopqrstuvwxyz', $index, 1);
	}
}
