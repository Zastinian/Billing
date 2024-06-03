<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;

class ServerController extends Controller
{
    public function active()
    {
        return view('admin.server.active', ['servers' => Server::where('status', 0)->get()]);
    }

    public function pending()
    {
        return view('admin.server.pending', ['servers' => Server::where('status', 1)->get()]);
    }

    public function suspended()
    {
        return view('admin.server.suspended', ['servers' => Server::where('status', 2)->get()]);
    }

    public function canceled()
    {
        return view('admin.server.canceled', ['servers' => Server::where('status', 3)->get()]);
    }

    public function show($id)
    {
        return view('admin.server.show', ['id' => $id, 'server' => Server::find($id)]);
    }
}
