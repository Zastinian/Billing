<?php

namespace App\Http\Middleware\Store;

use App\Models\Currency;
use App\Models\Tax;
use Closure;
use Illuminate\Http\Request;

class SetDefaultSession
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
        $this->setSession('currency', Currency::where('default', true)->first());
        $this->setSession('tax', Tax::where('country', 'Global')->first());

        return $next($request);
    }

    private function setSession($key, $value)
    {
        if (is_null(session($key))) {
            session([$key => $value]);
        }
    }
}
