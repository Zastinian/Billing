<?php

namespace App\Http\Middleware\Client;

use App\Models\Addon;
use App\Models\Plan;
use App\Models\Server;
use Closure;
use Illuminate\Http\Request;

class CheckServerAddonPermission
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
        if (is_null($addon = Addon::find($request->route('addon_id')))) return abort(404);

        return !in_array(Plan::find(Server::find($request->route('id'))->plan_id)->category_id, json_decode($addon->categories, true))
            ? abort(403) : $next($request);
    }
}
