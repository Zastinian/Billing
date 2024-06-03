<?php

namespace App\Http\Middleware\Admin;

use App\Models\Contact;
use Closure;
use Illuminate\Http\Request;

class CheckIfMessageExists
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
        $id = $request->route('msg_id');
        $message = Contact::find($id);

        if (is_null($message)) {
            return abort(404);
        } else {
            view()->share(['message' => $message]);
            return $next($request);
        }
    }
}
