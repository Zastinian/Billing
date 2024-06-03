<?php

namespace App\Http\Middleware\Admin;

use App\Models\KbCategory;
use Closure;
use Illuminate\Http\Request;

class CheckIfKbCategoryExists
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
        return (is_null(KbCategory::find($request->route('category_id'))))
            ? abort(404) : $next($request);
    }
}
