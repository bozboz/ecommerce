<?php

Route::filter('sessionProtect', function($route)
{
		$sessionId = $route->getParameter('sessionId');

		if (Session::token() !== $sessionId) {
			throw new Illuminate\Session\TokenMismatchException;
		}
});
