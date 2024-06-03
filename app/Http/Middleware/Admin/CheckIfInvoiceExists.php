<?php

namespace App\Http\Middleware\Admin;

use App\Models\Invoice;
use Closure;
use Illuminate\Http\Request;

class CheckIfInvoiceExists
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
        $invoice = Invoice::find($id);

        if (is_null($invoice)) {
            return abort(404);
        } else {
            view()->share(['invoice' => $invoice]);
            return $next($request);
        }
    }
}
