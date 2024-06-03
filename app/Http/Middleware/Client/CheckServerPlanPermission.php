<?php

namespace App\Http\Middleware\Client;

use App\Models\Plan;
use App\Models\Server;
use Closure;
use Illuminate\Http\Request;

class CheckServerPlanPermission
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
        if (is_null($plan = Plan::find($request->route('plan_id')))) return abort(404);

        return $plan->category_id !== Plan::find(Server::find($request->route('id'))->plan_id)->category_id
            ? abort(403) : $next($request);
    }
}
