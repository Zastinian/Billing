<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\Store\SetDefaultSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Store\WarnNonHttps::class
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin' => \App\Http\Middleware\Admin\AdminArea::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'check.admin.addon' => \App\Http\Middleware\Admin\CheckIfAddonExists::class,
        'check.admin.announce' => \App\Http\Middleware\Admin\CheckIfAnnouncementExists::class,
        'check.admin.affiliate' => \App\Http\Middleware\Admin\CheckIfAffiliateExists::class,
        'check.admin.category' => \App\Http\Middleware\Admin\CheckIfCategoryExists::class,
        'check.admin.client' => \App\Http\Middleware\Admin\CheckIfClientExists::class,
        'check.admin.coupon' => \App\Http\Middleware\Admin\CheckIfCouponExists::class,
        'check.admin.currency' => \App\Http\Middleware\Admin\CheckIfCurrencyExists::class,
        'check.admin.discount' => \App\Http\Middleware\Admin\CheckIfDiscountExists::class,
        'check.admin.invoice' => \App\Http\Middleware\Admin\CheckIfInvoiceExists::class,
        'check.admin.kb.category' => \App\Http\Middleware\Admin\CheckIfKbCategoryExists::class,
        'check.admin.kb.article' => \App\Http\Middleware\Admin\CheckIfKbArticleExists::class,
        'check.admin.message' => \App\Http\Middleware\Admin\CheckIfMessageExists::class,
        'check.admin.plan' => \App\Http\Middleware\Admin\CheckIfPlanExists::class,
        'check.admin.server' => \App\Http\Middleware\Admin\CheckIfServerExists::class,
        'check.admin.tax' => \App\Http\Middleware\Admin\CheckIfTaxExists::class,
        'check.admin.ticket' => \App\Http\Middleware\Admin\CheckIfTicketExists::class,
        'check.client.addon' => \App\Http\Middleware\Client\CheckServerAddonPermission::class,
        'check.client.invoice' => \App\Http\Middleware\Client\CheckInvoicePermission::class,
        'check.client.plan' => \App\Http\Middleware\Client\CheckServerPlanPermission::class,
        'check.client.server' => \App\Http\Middleware\Client\CheckServerPermission::class,
        'check.client.ticket' => \App\Http\Middleware\Client\CheckTicketPermission::class,
        'check.store.affiliate' => \App\Http\Middleware\Store\CheckIfAffiliateProgramEnabled::class,
        'check.store.order' => \App\Http\Middleware\Store\CheckPlanOrder::class,
        'close.register' => \App\Http\Middleware\Client\CloseRegistration::class,
        'csrf' => \App\Http\Middleware\VerifyCsrfToken::class,
        'demo' => \App\Http\Middleware\DenyInDemo::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'soon' => \App\Http\Middleware\ComingSoon::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
