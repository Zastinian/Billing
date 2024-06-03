<?php

namespace App\Http\Middleware\Admin;

use App\Models\Discount;
use Closure;
use Illuminate\Http\Request;

class CheckIfDiscountExists
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
        $discount = Discount::find($id);

        if (is_null($discount)) {
            return abort(404);
        } else {
            view()->share(['discount' => $discount]);
            return $next($request);
        }
    }
}
