<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/my';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            /** Store */
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/store.php'));

            /** Client Area */
            Route::prefix('/my')
                ->name('client.')
                ->middleware(['web', 'auth', 'verified'])
                ->namespace($this->namespace)
                ->group(base_path('routes/client.php'));

            /** Admin Area */
            Route::prefix('/admin')
                ->name('admin.')
                ->middleware(['web', 'auth', 'verified', 'admin'])
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));
            
            /**
             * API (Internal Use Only)
             */
            Route::prefix('/api/store')
                ->name('api.store.')
                ->middleware(['web', 'throttle:api'])
                ->namespace($this->namespace)
                ->group(base_path('routes/api/store.php'));
            
            Route::prefix('/api/client')
                ->name('api.client.')
                ->middleware(['web', 'auth', 'verified', 'throttle:api'])
                ->namespace($this->namespace)
                ->group(base_path('routes/api/client.php'));
            
            Route::prefix('/api/admin')
                ->name('api.admin.')
                ->middleware(['web', 'auth', 'verified', 'admin', 'throttle:api'])
                ->namespace($this->namespace)
                ->group(base_path('routes/api/admin.php'));

            /** Extensions */
            Route::prefix('/extension')
                ->name('extension.')
                ->middleware('web')
                ->namespace('Extensions')
                ->group(base_path('routes/extensions.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            if ($request->user()) {
                return $request->user()->is_admin ? Limit::none() : Limit::perMinute(120)->by($request->user()->id);
            }

            return Limit::perMinute(30)->by($request->ip());
        });
    }
}
