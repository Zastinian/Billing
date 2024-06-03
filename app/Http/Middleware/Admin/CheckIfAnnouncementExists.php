<?php

namespace App\Http\Middleware\Admin;

use App\Models\Announcement;
use Closure;
use Illuminate\Http\Request;

class CheckIfAnnouncementExists
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
        return (is_null(Announcement::find($request->route('id'))))
            ? abort(404) : $next($request);
    }
}
