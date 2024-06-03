<?php

use Illuminate\Support\Facades\Route;

/**
 * Admin Area
 */
// Dashboard
Route::get('/', 'Admin\DashController@show')->name('dash');

// Cache Config
Route::get('/cache', 'Admin\DashController@cache')->name('cache');

// Servers
Route::prefix('/servers')->name('servers.')->group(function () {
    Route::get('/active', 'Admin\ServerController@active')->name('active');
    Route::get('/pending', 'Admin\ServerController@pending')->name('pending');
    Route::get('/suspended', 'Admin\ServerController@suspended')->name('suspended');
    Route::get('/canceled', 'Admin\ServerController@canceled')->name('canceled');
});
Route::prefix('/server/{id}')->name('server.')->middleware('check.admin.server')->group(function () {
    Route::get('/', 'Admin\ServerController@show')->name('show');
});

// Clients
Route::prefix('/client')->name('client.')->group(function () {
    Route::get('/', 'Admin\ClientController@clients')->name('index');
    Route::post('/', 'Admin\ClientController@importUsers');

    Route::prefix('/{id}')->middleware('check.admin.client')->group(function () {
        Route::get('/', 'Admin\ClientController@client')->name('show');
        Route::get('/affiliates', 'Admin\ClientController@affiliates')->middleware('check.store.affiliate')->name('affiliates');
        Route::get('/credit', 'Admin\ClientController@credit')->name('credit');
    });
});

// Affiliate Program
Route::prefix('/affiliate')->middleware('check.store.affiliate')->name('affiliate.')->group(function () {
    Route::get('/', 'Admin\ClientController@affiliateProgram')->name('index');
    Route::get('/settings', 'Admin\ClientController@affiliateSetting')->withoutMiddleware('check.store.affiliate')->name('show');
});

// Server Plans
Route::prefix('/plan')->name('plan.')->group(function () {
    Route::get('/', 'Admin\PlanController@plans')->name('index');
    Route::get('/create', 'Admin\PlanController@createPlans')->name('create');

    Route::prefix('/view/{id}')->middleware('check.admin.plan')->group(function () {
        Route::get('/', 'Admin\PlanController@plan')->name('show');
    });
});

// Plan Categories
Route::prefix('/category')->name('category.')->group(function () {
    Route::get('/', 'Admin\PlanController@categories')->name('index');
    Route::get('/create', 'Admin\PlanController@createCategory')->name('create');

    Route::prefix('/view/{id}')->middleware('check.admin.category')->group(function () {
        Route::get('/', 'Admin\PlanController@category')->name('show');
    });
});

// Server Add-ons
Route::prefix('/addon')->name('addon.')->group(function () {
    Route::get('/', 'Admin\PlanController@addons')->name('index');
    Route::get('/create', 'Admin\PlanController@createAddon')->name('create');

    Route::prefix('/view/{id}')->middleware('check.admin.addon')->group(function () {
        Route::get('/', 'Admin\PlanController@addon')->name('show');
    });
});

// Discounts
Route::prefix('/discount')->name('discount.')->group(function () {
    Route::get('/', 'Admin\PlanController@discounts')->name('index');
    Route::get('/create', 'Admin\PlanController@createDiscount')->name('create');

    Route::prefix('/view/{id}')->middleware('check.admin.discount')->group(function () {
        Route::get('/', 'Admin\PlanController@discount')->name('show');
    });
});

// Coupon Codes
Route::prefix('/coupon')->name('coupon.')->group(function () {
    Route::get('/', 'Admin\PlanController@coupons')->name('index');
    Route::get('/create', 'Admin\PlanController@createCoupon')->name('create');

    Route::prefix('/view/{id}')->middleware('check.admin.coupon')->group(function () {
        Route::get('/', 'Admin\PlanController@coupon')->name('show');
    });
});

// Income
Route::get('/income', 'Admin\BillingController@income')->name('income');

// Invoices
Route::prefix('/invoice')->name('invoice.')->group(function () {
    Route::get('/', 'Admin\BillingController@invoices')->name('index');

    Route::prefix('/view/{id}')->middleware('check.admin.invoice')->group(function () {
        Route::get('/', 'Admin\BillingController@invoice')->name('show');
    });
});

// Currencies
Route::prefix('/currency')->name('currency.')->group(function () {
    Route::get('/', 'Admin\BillingController@currencies')->name('index');
    Route::get('/create', 'Admin\BillingController@createCurrency')->name('create');

    Route::prefix('/view/{id}')->middleware('check.admin.currency')->group(function () {
        Route::get('/', 'Admin\BillingController@currency')->name('show');
    });
});

// Taxes
Route::prefix('/tax')->name('tax.')->group(function () {
    Route::get('/', 'Admin\BillingController@taxes')->name('index');
    Route::get('/create', 'Admin\BillingController@createTax')->name('create');

    Route::prefix('/view/{id}')->middleware('check.admin.tax')->group(function () {
        Route::get('/', 'Admin\BillingController@tax')->name('show');
    });
});

// Support Tickets
Route::prefix('/ticket')->name('ticket.')->group(function () {
    Route::get('/', 'Admin\SupportController@tickets')->name('index');
    Route::get('/create', 'Admin\SupportController@createTicket')->name('create');

    Route::prefix('/view/{id}')->middleware('check.admin.ticket')->group(function () {
        Route::get('/', 'Admin\SupportController@ticket')->name('show');
    });
});

// Knowledge Base
Route::prefix('/kb')->name('kb.')->group(function () {
    Route::get('/', 'Admin\SupportController@kbCategories')->name('index');
    Route::get('/create', 'Admin\SupportController@createKbCategory')->name('create');

    Route::prefix('/category/{category_id}')->middleware('check.admin.kb.category')->group(function () {
        Route::get('/', 'Admin\SupportController@kbCategory')->name('show');

        Route::name('article.')->group(function () {
            Route::get('/create', 'Admin\SupportController@createKbArticle')->name('create');

            Route::prefix('/article/{article_id}')->middleware('check.admin.kb.article')->group(function () {
                Route::get('/', 'Admin\SupportController@KbArticle')->name('show');
            });
        });
    });
});

// Announcements
Route::prefix('/announce')->name('announce.')->group(function () {
    Route::get('/', 'Admin\SupportController@announcements')->name('index');
    Route::get('/create', 'Admin\SupportController@createAnnouncement')->name('create');

    Route::prefix('/view/{id}')->middleware('check.admin.announce')->group(function () {
        Route::get('/', 'Admin\SupportController@announcement')->name('show');
    });
});

// Store Settings
Route::prefix('/setting')->name('setting.')->group(function () {
    Route::get('/', 'Admin\SettingController@store')->name('show');
});

// Store Pages
Route::prefix('/page')->name('page.')->group(function () {
    Route::get('/contact', 'Admin\SettingController@contact')->name('contact');
    Route::get('/{name}', 'Admin\SettingController@page')->name('show');
    Route::get('/message/{msg_id}', 'Admin\SettingController@message')->middleware('check.admin.message')->name('message');
});

// Extensions
Route::prefix('/extension/{id}')->name('extension.')->group(function () {
    Route::get('/', 'Admin\ExtensionController@show')->name('show');
});
