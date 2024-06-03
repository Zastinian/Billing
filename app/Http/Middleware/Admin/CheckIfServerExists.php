<?php

namespace App\Http\Middleware\Admin;

use App\Models\Server;
use Closure;
use Illuminate\Http\Request;

class CheckIfServerExists
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
        $server = Server::find($id);

        if (is_null($server)) {
            return abort(404);
        } elseif ($server->status === 3) {
            return abort(404);
        } else {
            view()->share(['server' => $server]);
            return $next($request);
        }
    }
}
