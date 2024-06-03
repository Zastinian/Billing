<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Currency;
use App\Models\KbArticle;
use App\Models\KbCategory;
use App\Models\Plan;
use App\Models\Server;
use App\Models\Tax;

class StoreController extends Controller
{
    public function pages()
    {
        return view('store.pages');
    }

    public function contact()
    {
        return config('page.contact') ? view('store.contact') : abort(404);
    }

    public function plans($id = null)
    {
        if ($id) {
            if ($category = Category::find($id)) {
                return view('store.plans', ['category2' => $category, 'plans' => Plan::where('category_id', $category->id)->orderBy('order', 'asc')->get()]);
            }
            return abort(404);
        }
        return Category::all()->count() ? view('store.plans', ['plans' => Plan::all()])
            : back()->with('warning_msg', 'Sorry, but no server plans are available!');
    }

    public function order($id)
    {
        $plan_find = Plan::find($id);
        if(is_null($plan_find->global_limit)) {
            return view('store.order', ['id' => $id, 'plan' => Plan::find($id)]);
        } else {
            $servers = 0;
            foreach (Plan::where('category_id', $id)->get() as $plan_find) {
                $servers += Server::where('plan_id', $plan_find->id)->where(function ($query) { $query->where('status', 0)->orWhere('status', 1); })->count();
            }
            $plan_number = number_format($plan_find->global_limit);
            $servers_number = number_format($servers);
            $total = $plan_find->global_limit - $servers;
            if($total == 0) {
                return redirect()->route('plans');
            } else {
                return view('store.order', ['id' => $id, 'plan' => Plan::find($id)]);
            }
        }
    }

    public function checkout($id)
    {
        if (!session()->has("order_server_$id"))
            return redirect()->route('order', ['id' => $id]);

        return view('store.checkout', ['id' => $id, 'plan' => Plan::find($id)]);
    }

    public function kb($id = null)
    {
        if ($id) {
            if ($article = KbArticle::find($id)) {
                return view('store.article', ['article' => $article]);
            }
            return abort(404);
        }

        return (KbCategory::all()->count()) ? view('store.kb')
            : back()->with('warning_msg', 'Sorry, but no support articles are available!');
    }

    public function affiliate($id)
    {
        if ($client = Client::find($id)) {
            if (is_null(session('referer_id'))) {
                session(['referer_id' => $id]);
                $client->clicks += 1;
                $client->save();
            }
        }
        return redirect()->route('home');
    }

    public function currency($id)
    {
        if (is_null($currency = Currency::find($id))) {
            $currency = Currency::where('default', true)->first();
        }
        session(['currency' => $currency]);
        return back();
    }

    public function country($id)
    {
        if (is_null($tax = Tax::find($id))) {
            $tax = Tax::where('country', 'Global')->first();
        }
        session(['tax' => $tax]);
        return back();
    }

    public function lang()
    {
        /**
         * Coming soon...
         */

        return back();
    }

    public function reset($token)
    {
        return view('client.reset', ['token' => $token]);
    }
}
