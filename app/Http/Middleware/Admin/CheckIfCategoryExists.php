<?php

namespace App\Http\Middleware\Admin;

use App\Models\Category;
use Closure;
use Illuminate\Http\Request;

class CheckIfCategoryExists
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
        $category = Category::find($id);

        if (is_null($category)) {
            return abort(404);
        } else {
            view()->share(['category' => $category]);
            return $next($request);
        }
    }
}
