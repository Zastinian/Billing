<?php

use Illuminate\Support\Facades\Route;

Route::any('/extension/mercadopago/ipn', 'Extensions\Gateways\MercadoPago\Controller@ipn')->name('mercadopago.ipn');
