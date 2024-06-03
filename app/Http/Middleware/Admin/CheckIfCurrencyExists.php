<?php

namespace App\Http\Middleware\Admin;

use App\Models\Currency;
use Closure;
use Illuminate\Http\Request;

class CheckIfCurrencyExists
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
        $id = $request->route('id');
        $currency = Currency::find($id);

        if (is_null($currency)) {
            return abort(404);
        } else {
            view()->share(['currency' => $currency]);
            return $next($request);
        }
    }
}
