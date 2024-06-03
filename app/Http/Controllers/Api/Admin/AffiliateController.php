<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\AffiliateEarning;
use App\Models\AffiliateProgram;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends ApiController
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enabled' => 'required|boolean',
            'conversion' => 'required|numeric|gt:0|lte:100',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        
        $affiliate_enabled = AffiliateProgram::where('key', 'enabled')->first();
        $affiliate_enabled->value = $request->input('enabled') === '1';
        $affiliate_enabled->save();
        
        $affiliate_conversion = AffiliateProgram::where('key', 'conversion')->first();
        $affiliate_conversion->value = $request->input('conversion');
        $affiliate_conversion->save();

        return $this->respondJson(['success' => 'You have updated the affiliate program settings successfully! Reloading configurations...']);
    }

    public function accept($id)
    {
        $affiliate = AffiliateEarning::find($id);
        $affiliate->status = 0;
        $affiliate->save();

        $client = Client::find($affiliate->client_id);
        $client->credit += $affiliate->commission;
        $client->save();

        return $this->respondJson(['success' => 'You have accepted the withdrawal request. The commission has been added to the account credit balance.']);
    }

    public function reject($id)
    {
        $affiliate = AffiliateEarning::find($id);
        $affiliate->status = 2;
        $affiliate->save();

        return $this->respondJson(['success' => 'You have rejected the request.']);
    }
}
