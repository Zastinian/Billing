<?php

namespace App\Http\Middleware\Store;

use App\Models\Plan;
use Closure;
use Illuminate\Http\Request;

class CheckPlanOrder
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
        if (is_null($plan = Plan::find($request->route('id')))) return abort(404);

        if (!Plan::verifyPlan($plan)) {
            return back()->with(['warning_msg', 'The server plan is currently out of stock.']);
        }

        if ($client = $request->user()) {
            if (!Plan::verifyPlan($plan, $client)) {
                return back()->with(['danger_msg', 'You have reached the maximum limit of servers using this server plan!']);
            }
        }

        return $next($request);
    }
}
