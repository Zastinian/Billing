<?php

use Illuminate\Support\Facades\Route;

Route::any('/extension/paypal/ipn', 'Extensions\Gateways\PayPal\Controller@ipn')->name('paypal.ipn');
