<?php

use Illuminate\Support\Facades\Route;

Route::post('/order/{id}', 'Api\StoreController@order')->name('order');
Route::post('/order/{id}/summary', 'Api\StoreController@summary')->name('order.summary');
Route::post('/checkout/{id}', 'Api\StoreController@checkout')->name('checkout');
Route::post('/contact', 'Api\StoreController@contact')->name('contact');
Route::get('/payment', 'Api\StoreController@payment')->name('payment');

Route::post('/login', 'Api\AuthController@login')->name('login');
Route::post('/register', 'Api\AuthController@register')->middleware('close.register')->name('register');
Route::post('/forgot', 'Api\AuthController@forgot')->name('forgot');
Route::post('/reset', 'Api\AuthController@reset')->name('reset');
