<?php

use App\Models\Setting;

$setting_model = Setting::class;

try {
    $company_name = $setting_model::where('key', 'company_name')->value('value');
    $url = $setting_model::where('key', 'store_url')->value('value');
    $logo_file_path = $setting_model::where('key', 'logo_path')->value('value');
    $favicon_file_path = $setting_model::where('key', 'favicon_path')->value('value');
    $dark_mode = $setting_model::where('key', 'dark_mode')->value('value');
    $open_registration = $setting_model::where('key', 'open_registration')->value('value');
    $panel_url = $setting_model::where('key', 'panel_url')->value('value');
    $panel_client_api_key = $setting_model::where('key', 'panel_client_api_key')->value('value');
    $panel_app_api_key = $setting_model::where('key', 'panel_app_api_key')->value('value');
    $phpmyadmin_url = $setting_model::where('key', 'phpmyadmin_url')->value('value');
    $hcaptcha_site_key = $setting_model::where('key', 'hcaptcha_site_key')->value('value');
    $hcaptcha_secret_key = $setting_model::where('key', 'hcaptcha_secret_key')->value('value');
    $google_analytics_id = $setting_model::where('key', 'google_analytics_id')->value('value');
    $arc_widget_id = $setting_model::where('key', 'arc_widget_id')->value('value');
} catch (Throwable $err) {
    $company_name = 'Company Name';
    $url = 'https://example.com';
    $logo_file_path = '/img/icon.webp';
    $favicon_file_path = '/img/favicon.webp';
    $dark_mode = 'true';
    $open_registration = 'true';
    $panel_url = 'https://panel.example.com';
    $panel_client_api_key = null;
    $panel_app_api_key = null;
    $phpmyadmin_url = 'https://pma.example.com';
    $hcaptcha_site_key = '';
    $hcaptcha_secret_key = '';
    $google_analytics_id = null;
    $arc_widget_id = '';
}

return [
    'version' => 'v1.0.0',
    'company_name' => $company_name,
    'logo_file_path' => $logo_file_path,
    'favicon_file_path' => $favicon_file_path,
    'dark_mode' => $dark_mode,
    'open_registration' => $open_registration,
    'panel_url' => $panel_url,
    'panel_client_api_key' => $panel_client_api_key,
    'panel_app_api_key' => $panel_app_api_key,
    'phpmyadmin_url' => $phpmyadmin_url,
    'hcaptcha_site_key' => $hcaptcha_site_key,
    'hcaptcha_secret_key' => $hcaptcha_secret_key,
    'google_analytics_id' => $google_analytics_id,
    'arc_widget_id' => $arc_widget_id,
    'name' => 'HedystiaBilling',
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => $url,
    'asset_url' => env('ASSET_URL', null),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Srmklive\PayPal\Providers\PayPalServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
    'aliases' => [
        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        //'Redis' => Illuminate\Support\Facades\Redis::class, // Incompatible with PHP Redis extension
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'PayPal' => Srmklive\PayPal\Facades\PayPal::class,

    ],

];
