<?php

namespace App\Http\Middleware\Admin;

use App\Models\KbArticle;
use Closure;
use Illuminate\Http\Request;

class CheckIfKbArticleExists
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
        return (is_null(KbArticle::find($request->route('article_id'))))
            ? abort(404) : $next($request);
    }
}
