<?php

use Illuminate\Support\Facades\Route;

Route::post('/mercadopago/ipn', 'Extensions\Gateways\MercadoPago\Controller@ipn')->name('mercadopago.ipn');
