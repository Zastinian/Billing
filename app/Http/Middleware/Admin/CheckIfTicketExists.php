<?php

namespace App\Http\Middleware\Admin;

use App\Models\Ticket;
use Closure;
use Illuminate\Http\Request;

class CheckIfTicketExists
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
        $ticket = Ticket::find($id);

        if (is_null($ticket)) {
            return abort(404);
        } else {
            view()->share(['ticket' => $ticket]);
            return $next($request);
        }
    }
}
