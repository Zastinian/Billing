<?php

namespace App\Http\Middleware\Client;

use App\Models\Server;
use Closure;
use Illuminate\Http\Request;

class CheckServerPermission
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
        return is_null($server = Server::find($request->route('id')))
            || $server->client_id !== $request->user()->id || $server->status !== 0
            ? abort(403) : $next($request);
    }
}
