<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'global_limit',
        'per_client_limit',
        'per_client_trial_limit',
        'order',
    ];

    public static function verifyCategory(self $category, Client $client = null)
    {
        if ($client && $category->per_client_limit) {
            $servers = 0;
            foreach (Plan::where('category_id', $category->id)->get() as $plan) {
                $servers += Server::where('client_id', $client->id)->where('plan_id', $plan->id)->where(function ($query) { $query->where('status', 0)->orWhere('status', 1); })->count();
            }
            if ($servers >= $category->per_client_limit) {
                return false;
            }
        }

        if (is_null($category->global_limit)) return true;

        $servers = 0;
        foreach (Plan::where('category_id', $category->id)->get() as $plan) {
            $servers += Server::where('plan_id', $plan->id)->where(function ($query) { $query->where('status', 0)->orWhere('status', 1); })->count();
        }
        return $servers < $category->global_limit;
    }

    public static function verifyCategoryTrial(self $category, Client $client)
    {
        if (is_null($category->per_client_trial_limit)) return true;

        $servers = 0;
        foreach (Plan::where('category_id', $category->id)->get() as $plan) {
            $servers += Server::where('client_id', $client->id)->where('plan_id', $plan->id)->count();
        }
        return $servers < $category->per_client_trial_limit;
    }
}
