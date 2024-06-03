<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Income;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Artisan;

class DashController extends Controller
{
    public function show()
    {
        $period = CarbonPeriod::create(Carbon::now()->subDays(30), Carbon::now());
        $incomes = $orders = $clients = [];

        foreach ($period as $date) {
            $format = Carbon::parse($date)->day;
            $incomes[$format] = $orders[$format] = $clients[$format] = 0;
            $day = [Carbon::parse($date)->format('Y-m-d'), Carbon::parse($date)->setHour(23)->setMinute(59)->setSecond(59)];

            foreach (Income::whereBetween('created_at', $day)->get() as $income) {
                $incomes[$format] += $income->price;
                $orders[$format] += 1;
            }

            $clients[$format] = count(Client::whereDate('created_at', $day)->get());
        }

        return view('admin.dash', ['title' => 'Admin Dashboard', 'incomes' => $incomes, 'orders' => $orders, 'clients' => $clients]);
    }

    public function cache()
    {
        if (Artisan::call('config:cache') != 0)
            return back()->with('danger_msg', 'Failed to clear cached configurations!');
        
        if (Artisan::call('queue:restart') != 0)
            return back()->with('danger_msg', 'Cleared cached configurations but failed to restart queue workers!');
        
        return back()->with('success_msg', 'Cleared cached configurations and restarted queue workers successfully!');
    }
}
