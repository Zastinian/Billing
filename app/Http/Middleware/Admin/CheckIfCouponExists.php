<?php

namespace App\Http\Middleware\Admin;

use App\Models\Coupon;
use Closure;
use Illuminate\Http\Request;

class CheckIfCouponExists
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
        $coupon = Coupon::find($id);

        if (is_null($coupon)) {
            return abort(404);
        } else {
            view()->share(['coupon' => $coupon]);
            return $next($request);
        }
    }
}
