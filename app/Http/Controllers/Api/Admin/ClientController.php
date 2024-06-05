<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Jobs\DeletePanelUser;
use App\Jobs\EditPanelUserEmail;
use App\Jobs\SuspendServer;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Currency;
use App\Models\Server;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientController extends ApiController
{
    public function basic(Request $request, $id)
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

        $client = Client::find($id);
        $client->currency = $currency->name;
        $client->country = $tax->country;
        $client->auto_renew = ($request->input('auto_renew') === '1');
        $client->save();

        session([
            'currency' => $currency,
            'tax' => $tax,
        ]);

        return $this->respondJson(['success' => 'The account settings have been updated!']);
    }

    public function email(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255|unique:clients',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $client = Client::find($id);
        $client->email = $request->input('email');
        $client->save();

        EditPanelUserEmail::dispatch($client->user_id, $request->input('email'))->onQueue('high');

        return $this->respondJson(['success' => 'The email address has been updated!']);
    }

    public function password(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|confirmed|min:8|max:255',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $client = Client::find($id);
        $client->password = Hash::make($request->input('password'));
        $client->save();

        return $this->respondJson(['success' => 'The account password has been updated!']);
    }

    public function suspend($id)
    {
        $client = Client::find($id);
        $client->is_active = false;
        $client->save();

        foreach (Server::where('client_id', $id)->get() as $server) {
            SuspendServer::dispatch($server->id);
        }

        return $this->respondJson(['success' => 'You have suspended the client! The servers are also going to be suspended.']);
    }

    public function unsuspend($id)
    {
        $client = Client::find($id);
        $client->is_active = true;
        $client->save();

        return $this->respondJson(['success' => 'You have unsuspended the client!']);
    }

    public function promote($id)
    {
        $client = Client::find($id);
        $client->is_admin = true;
        $client->save();

        return $this->respondJson(['success' => 'You have promoted the client to administrator!']);
    }

    public function demote($id)
    {
        $client = Client::find($id);
        $client->is_admin = false;
        $client->save();

        return $this->respondJson(['success' => 'You have demoted the client!']);
    }

    public function delete($id)
    {
        $client = Client::find($id);
        $client->delete();

        DeletePanelUser::dispatch($id);

        return $this->respondJson(['success' => 'You have deleted the client! The panel account is also going to be deleted.']);
    }

    public function credit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'credit' => 'required|numeric|gte:0',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $client = Client::find($id);

        Credit::create([
            'client_id' => $request->user()->id,
            'details' => 'Edited by an administrator',
            'change' => $request->input('credit') - $client->credit,
            'balance' => $request->input('credit'),
        ]);

        $client->credit = $request->input('credit');
        $client->save();

        return $this->respondJson(['success' => 'You have updated the credit balance.']);
    }
}
