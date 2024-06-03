<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportPanelUsers;
use App\Models\Client;

class ClientController extends Controller
{
    public function clients()
    {
        return view('admin.client.index', ['clients' => Client::all()]);
    }

    public function importUsers()
    {
        ImportPanelUsers::dispatch();
        return back()->with('success_msg', 'Users are being imported in the background. You may leave this page and check back later.');
    }

    public function client($id)
    {
        return view('admin.client.show', ['id' => $id, 'client' => Client::find($id)]);
    }

    public function affiliates($id)
    {
        return view('admin.client.affiliates', ['id' => $id]);
    }

    public function credit($id)
    {
        return view('admin.client.credit', ['id' => $id]);
    }

    public function affiliateProgram()
    {
        return view('admin.affiliate.index');
    }

    public function affiliateSetting()
    {
        return view('admin.affiliate.show');
    }
}
