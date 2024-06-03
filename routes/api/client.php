<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/{id}')->group(function () {
    Route::prefix('/plan')->name('plan.')->group(function () {
        Route::post('/{plan_id}/change', 'Api\Client\PlanController@change')->name('change');
        Route::post('/{plan_id}/checkout', 'Api\Client\PlanController@checkout')->name('checkout');
        Route::delete('/', 'Api\Client\PlanController@cancel')->name('cancel');
    });
    
    Route::prefix('/addon/{addon_id}')->name('addon.')->group(function () {
        Route::post('/add', 'Api\Client\AddonController@add')->name('add');
        Route::post('/checkout', 'Api\Client\AddonController@checkout')->name('checkout');
        Route::delete('/', 'Api\Client\AddonController@remove')->name('remove');
    });
});

Route::prefix('/invoice/{id}')->name('invoice.')->group(function () {
    Route::post('/', 'Api\Client\InvoiceController@pay')->name('pay');
});

Route::prefix('/ticket')->name('ticket.')->group(function () {
    Route::post('/', 'Api\Client\TicketController@create')->name('create');
    Route::put('/{id}', 'Api\Client\TicketController@update')->name('update');
});

Route::prefix('/account')->name('account.')->group(function () {
    Route::put('/basic', 'Api\Client\AccountController@setting')->name('basic');
    Route::put('/email', 'Api\Client\AccountController@email')->name('email');
    Route::put('/password', 'Api\Client\AccountController@password')->name('password');
});

Route::prefix('/credit')->name('credit.')->group(function () {
    Route::post('/', 'Api\Client\CreditController@add')->name('add');
});
