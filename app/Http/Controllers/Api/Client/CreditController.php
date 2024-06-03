<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\ApiController;
use App\Jobs\IssueCreditInvoice;
use App\Models\Currency;
use App\Models\Invoice;
use Extensions\ExtensionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreditController extends ApiController
{
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'credit' => 'required|numeric|gt:0',
            'gateway' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        if (is_null($extension = ExtensionManager::getExtension($request->input('gateway'))))
            return $this->respondJson(['error' => 'The payment gateway is invalid!']);

        IssueCreditInvoice::dispatchSync($id = $request->user()->id, $credit = price($request->input('credit'), Currency::VALUE_ONLY), $extension::$display_name);

        session(['payment_invoice' => $invoice = Invoice::where('client_id', $id)->where('credit', $credit)->latest()->first()]);

        $link_pay = $extension::checkout($invoice, route('client.credit.show'));

        Invoice::where('client_id', $id)->where('credit', $credit)->latest()->first()->update(['payment_link' => $link_pay]);

        return $this->respondJson(['info' => 'You\'re being redirected to a third-party payment gateway...',
            'url' => $link_pay,
        ]);
    }
}
