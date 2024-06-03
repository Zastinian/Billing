<?php

namespace App\Http\Middleware\Store;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class WarnNonHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        View::share('secure', $request->secure() || $request->server('HTTP_X_FORWARDED_PROTO') == 'https' ? true : false);
        return $next($request);
    }
}
