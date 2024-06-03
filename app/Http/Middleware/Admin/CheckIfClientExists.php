<?php

namespace App\Http\Middleware\Admin;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;

class CheckIfClientExists
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
        $client = Client::find($id);

        if (is_null($client)) {
            return abort(404);
        } else {
            view()->share(['client' => $client]);
            return $next($request);
        }
    }
}
