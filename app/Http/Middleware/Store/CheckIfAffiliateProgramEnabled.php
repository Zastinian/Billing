<?php

namespace App\Http\Middleware\Store;

use Closure;
use Illuminate\Http\Request;

class CheckIfAffiliateProgramEnabled
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
        if (!config('affiliate.enabled')) {
            if ($request->expectsJson()) return abort(400);

            return back()->with('warning_msg', 'The affiliate program has been disabled.');
        }
        return $next($request);
    }
}
