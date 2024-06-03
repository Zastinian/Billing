<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\ApiController;
use App\Jobs\EditPanelUserEmail;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends ApiController
{
    public function setting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'required|string|exists:currencies,id',
            'country' => 'required|string|exists:taxes,id',
            'auto_renew' => 'required|boolean',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $currency = Currency::find($request->input('currency'));
        $tax = Tax::find($request->input('country'));
        
        $client = Client::find($request->user()->id);
        $client->currency = $currency->name;
        $client->country = $tax->country;
        $client->auto_renew = ($request->input('auto_renew') === '1');
        $client->save();

        session([
            'currency' => $currency,
            'tax' => $tax,
        ]);

        return $this->respondJson(['success' => 'Your account settings have been updated!']);
    }
    
    public function email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255|unique:clients',
            'password' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $client = Client::find($request->user()->id);

        if (!Hash::check($request->input('password'), $client->password))
            return $this->respondJson(['error' => 'The current password is incorrect!']);

        $client->email = $request->input('email');
        $client->save();

        EditPanelUserEmail::dispatch($client->user_id, $request->input('email'))->onQueue('high');

        return $this->respondJson(['success' => 'Your email address has been updated! The email of your panel account is also going to be updated.']);
    }
    
    public function password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current' => 'required|string',
            'password' => 'required|string|confirmed|min:8|max:255',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $client = Client::find($request->user()->id);

        if (!Hash::check($request->input('current'), $client->password))
            return $this->respondJson(['error' => 'The current password is incorrect!']);
        
        $client->password = Hash::make($request->input('password'));
        $client->save();
        return $this->respondJson(['success' => 'Your account password has been updated!']);
    }
}
