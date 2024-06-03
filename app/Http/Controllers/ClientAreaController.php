<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;

class ClientAreaController extends Controller
{
    public function dash()
    {
        return view('client.dash');
    }

    public function servers()
    {
        return view('client.server.index');
    }

    public function server($id)
    {
        return view('client.server.show', ['id' => $id, 'server' => Server::find($id)]);
    }

    public function plan($id)
    {
        return view('client.server.plan.show', ['id' => $id, 'server' => Server::find($id)]);
    }

    public function changePlan($id, $plan_id)
    {
        return view('client.server.plan.change', ['id' => $id, 'plan_id' => $plan_id, 'server' => Server::find($id)]);
    }

    public function planCheckOut($id, $plan_id)
    {
        return view('client.server.plan.checkout', ['id' => $id, 'plan_id' => $plan_id, 'server' => Server::find($id)]);
    }

    public function addon($id)
    {
        return view('client.server.addon.show', ['id' => $id, 'server' => Server::find($id)]);
    }

    public function addAddon($id, $plan_id)
    {
        return view('client.server.addon.add', ['id' => $id, 'plan_id' => $plan_id, 'server' => Server::find($id)]);
    }

    public function addonCheckout($id, $plan_id)
    {
        return view('client.server.addon.checkout', ['id' => $id, 'plan_id' => $plan_id, 'server' => Server::find($id)]);
    }

    public function invoices()
    {
        return view('client.invoice.index');
    }

    public function invoice($id)
    {
        return view('client.invoice.show', ['id' => $id, 'invoice' => Invoice::find($id)]);
    }

    public function printInvoice($id)
    {
        return view('mail.invoice', ['title' => "Invoice #${id}", 'id' => $id]);
    }

    public function payInvoice($id)
    {
        return view('client.invoice.pay', ['id' => $id]);
    }

    public function tickets()
    {
        return view('client.ticket.index');
    }

    public function createTicket()
    {
        return view('client.ticket.create');
    }

    public function ticket($id)
    {
        return view('client.ticket.show', ['id' => $id]);
    }

    public function affiliate()
    {
        return view('client.affiliate');
    }

    public function account()
    {
        return view('client.account');
    }

    public function credit()
    {
        return view('client.credit');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success_msg', 'You have logged out successfully!');
    }
}
