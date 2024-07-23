<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('bearer', function (Request $request, $expression) {
            $token = ($expression == 'user') ? Crypt::encryptString($request->user()->password) : Crypt::encryptString(config('app.key'));
            return "<?php echo $token ?>";
        });
    }
}
