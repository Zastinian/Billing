<?php

namespace App\Http\Middleware\Admin;

use App\Models\Tax;
use Closure;
use Illuminate\Http\Request;

class CheckIfTaxExists
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
        $tax = Tax::find($id);

        if (is_null($tax)) {
            return abort(404);
        } else {
            view()->share(['tax' => $tax]);
            return $next($request);
        }
    }
}
