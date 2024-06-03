<?php

namespace App\Http\Middleware\Admin;

use App\Models\Addon;
use Closure;
use Illuminate\Http\Request;

class CheckIfAddonExists
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
        $addon = Addon::find($id);

        if (is_null($addon)) {
            return abort(404);
        } else {
            view()->share(['addon' => $addon]);
            return $next($request);
        }
    }
}
