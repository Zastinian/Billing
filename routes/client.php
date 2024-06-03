<?php

use Illuminate\Support\Facades\Route;

/**
 * Client Area
 */
// Dashboard
Route::get('/', 'ClientAreaController@dash')->name('dash');

// Servers
Route::prefix('/server')->name('server.')->group(function () {
    // List all servers
    Route::get('/', 'ClientAreaController@servers')->name('index');

    Route::prefix('/{id}')->middleware('check.client.server')->group(function () {
        // Show server information
        Route::get('/', 'ClientAreaController@server')->name('show');

        // Manage server plan
        Route::prefix('/plan')->name('plan.')->group(function () {
            Route::get('/', 'ClientAreaController@plan')->name('show');
            Route::get('/{plan_id}/change', 'ClientAreaController@changePlan')->middleware('check.client.plan')->name('change');
            Route::get('/{plan_id}/checkout', 'ClientAreaController@planCheckout')->middleware('check.client.plan')->name('checkout');
        });

        // Manage server add-ons
        Route::prefix('/addon')->name('addon.')->group(function () {
            Route::get('/', 'ClientAreaController@addon')->name('show');
            Route::get('/{addon_id}/add', 'ClientAreaController@addAddon')->middleware('check.client.addon')->name('add');
            Route::get('/{addon_id}/checkout', 'ClientAreaController@addonCheckout')->middleware('check.client.addon')->name('checkout');
        });
    });
});

// Invoices
Route::prefix('/invoice')->name('invoice.')->group(function () {
    Route::get('/', 'ClientAreaController@invoices')->name('index');

    Route::prefix('/{id}')->middleware('check.client.invoice')->group(function () {
        Route::get('/', 'ClientAreaController@invoice')->name('show');
        Route::get('/print', 'ClientAreaController@printInvoice')->name('print');
        Route::get('/pay', 'ClientAreaController@payInvoice')->name('pay');
    });
});

// Support Tickets
Route::prefix('/ticket')->name('ticket.')->group(function () {
    Route::get('/', 'ClientAreaController@tickets')->name('index');
    Route::get('/create','ClientAreaController@createTicket')->name('create');

    Route::prefix('/view/{id}')->middleware('check.client.ticket')->group(function () {
        Route::get('/', 'ClientAreaController@ticket')->name('show');
    });
});

// Affiliate Program
Route::prefix('/affiliate')->middleware('check.store.affiliate')->name('affiliate.')->group(function () {
    Route::get('/', 'ClientAreaController@affiliate')->name('show');
});

// Account Settings
Route::prefix('/account')->name('account.')->group(function () {
    Route::get('/', 'ClientAreaController@account')->name('show');
});

// Account Credit
Route::prefix('/credit')->name('credit.')->group(function () {
    Route::get('/', 'ClientAreaController@credit')->name('show');
});

// Logout
Route::get('/logout', 'ClientAreaController@logout')->withoutMiddleware('verified')->name('logout');
