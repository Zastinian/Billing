<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/server/{id}')->name('server.')->group(function () {
    Route::post('/suspend', 'Api\Admin\ServerController@suspend')->name('suspend');
    Route::post('/unsuspend', 'Api\Admin\ServerController@unsuspend')->name('unsuspend');
    Route::post('/delete', 'Api\Admin\ServerController@delete')->name('delete');
});

Route::prefix('/client')->name('client.')->group(function () {
    Route::put('/basic/{id}', 'Api\Admin\ClientController@basic')->name('basic');
    Route::put('/email/{id}', 'Api\Admin\ClientController@email')->name('email');
    Route::put('/password/{id}', 'Api\Admin\ClientController@password')->name('password');
    Route::post('/suspend/{id}', 'Api\Admin\ClientController@suspend')->name('suspend');
    Route::post('/unsuspend/{id}', 'Api\Admin\ClientController@unsuspend')->name('unsuspend');
    Route::post('/promote/{id}', 'Api\Admin\ClientController@promote')->name('promote');
    Route::post('/demote/{id}', 'Api\Admin\ClientController@demote')->name('demote');
    Route::delete('/{id}', 'Api\Admin\ClientController@delete')->name('delete');
    Route::put('/credit/{id}', 'Api\Admin\ClientController@credit')->name('credit');
});

Route::prefix('/affiliate')->name('affiliate.')->group(function () {
    Route::put('/', 'Api\Admin\AffiliateController@update')->name('update');
    Route::post('/accept/{id}', 'Api\Admin\AffiliateController@accept')->middleware(['check.store.affiliate'])->name('accept');
    Route::post('/reject/{id}', 'Api\Admin\AffiliateController@reject')->middleware(['check.store.affiliate'])->name('reject');
});

Route::prefix('/plan')->name('plan.')->group(function () {
    Route::post('/', 'Api\Admin\PlanController@create')->name('create');
    Route::put('/{id}', 'Api\Admin\PlanController@update')->name('update');
    Route::delete('/{id}', 'Api\Admin\PlanController@delete')->name('delete');
});

Route::prefix('/category')->name('category.')->group(function () {
    Route::post('/', 'Api\Admin\CategoryController@create')->name('create');
    Route::put('/{id}', 'Api\Admin\CategoryController@update')->name('update');
    Route::delete('/{id}', 'Api\Admin\CategoryController@delete')->name('delete');
});

Route::prefix('/addon')->name('addon.')->group(function () {
    Route::post('/', 'Api\Admin\AddonController@create')->name('create');
    Route::put('/{id}', 'Api\Admin\AddonController@update')->name('update');
    Route::delete('/{id}', 'Api\Admin\AddonController@delete')->name('delete');
});

Route::prefix('/discount')->name('discount.')->group(function () {
    Route::post('/', 'Api\Admin\DiscountController@create')->name('create');
    Route::put('/{id}', 'Api\Admin\DiscountController@update')->name('update');
    Route::delete('/{id}', 'Api\Admin\DiscountController@delete')->name('delete');
});

Route::prefix('/coupon')->name('coupon.')->group(function () {
    Route::post('/', 'Api\Admin\CouponController@create')->name('create');
    Route::put('/{id}', 'Api\Admin\CouponController@update')->name('update');
    Route::delete('/{id}', 'Api\Admin\CouponController@delete')->name('delete');
});

Route::prefix('/invoice')->name('invoice.')->group(function () {
    Route::post('/{id}', 'Api\Admin\InvoiceController@paid')->name('paid');
});

Route::prefix('/currency')->name('currency.')->group(function () {
    Route::post('/', 'Api\Admin\CurrencyController@create')->name('create');
    Route::put('/{id}', 'Api\Admin\CurrencyController@update')->name('update');
    Route::delete('/{id}', 'Api\Admin\CurrencyController@delete')->name('delete');
});

Route::prefix('/tax')->name('tax.')->group(function () {
    Route::post('/', 'Api\Admin\TaxController@create')->name('create');
    Route::put('/{id}', 'Api\Admin\TaxController@update')->name('update');
    Route::delete('/{id}', 'Api\Admin\TaxController@delete')->name('delete');
});

Route::prefix('/ticket')->name('ticket.')->group(function () {
    Route::post('/', 'Api\Admin\TicketController@create')->name('create');
    Route::put('/{id}', 'Api\Admin\TicketController@update')->name('update');
});

Route::prefix('/kb')->name('kb.')->group(function () {
    Route::post('/', 'Api\Admin\KbCategoryController@create')->name('create');
    Route::put('/{category_id}', 'Api\Admin\KbCategoryController@update')->name('update');
    Route::delete('/{category_id}', 'Api\Admin\KbCategoryController@delete')->name('delete');

    Route::prefix('/{category_id}')->name('article.')->group(function () {
        Route::post('/', 'Api\Admin\KbArticleController@create')->name('create');
        Route::put('/{article_id}', 'Api\Admin\KbArticleController@update')->name('update');
        Route::delete('/{article_id}', 'Api\Admin\KbArticleController@delete')->name('delete');
    });
});

Route::prefix('/announcement')->name('announce.')->group(function () {
    Route::post('/', 'Api\Admin\AnnouncementController@create')->name('create');
    Route::put('/{id}', 'Api\Admin\AnnouncementController@update')->name('update');
    Route::delete('/{id}', 'Api\Admin\AnnouncementController@delete')->name('delete');
});

Route::prefix('/setting')->name('setting.')->group(function () {
    Route::put('/store', 'Api\Admin\SettingController@store')->name('store');
    Route::put('/page', 'Api\Admin\SettingController@page')->name('page');
});

// Extensions
Route::prefix('/extension/{id}')->name('extension.')->group(function () {
    Route::put('/', 'Admin\ExtensionController@store')->name('update');
});
