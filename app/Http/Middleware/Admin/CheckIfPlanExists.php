<?php

namespace App\Http\Middleware\Admin;

use App\Models\Plan;
use Closure;
use Illuminate\Http\Request;

class CheckIfPlanExists
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
        $plan = Plan::find($id);

        if (is_null($plan)) {
            return abort(404);
        } else {
            view()->share(['plan' => $plan]);
            return $next($request);
        }
    }
}
