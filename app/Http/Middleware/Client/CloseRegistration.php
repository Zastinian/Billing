<?php

namespace App\Http\Middleware\Client;

use Closure;
use Illuminate\Http\Request;

class CloseRegistration
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
        if (!config('app.open_registration')) {
            return abort(403);
        }
        
        return $next($request);
    }
}
