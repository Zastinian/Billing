<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'ram',
        'cpu',
        'disk',
        'swap',
        'io',
        'databases',
        'backups',
        'extra_ports',
        'locations_nodes_id',
        'min_port',
        'max_port',
        'nests_eggs_id',
        'server_description',
        'discount',
        'coupons',
        'days_before_suspend',
        'days_before_delete',
        'global_limit',
        'per_client_limit',
        'per_client_trial_limit',
        'order',
    ];

    public static function verifyPlan(self $plan, Client $client = null)
    {
        $category_model = new Category;
        $category = Category::find($plan->category_id);
        if ($client && $plan->per_client_limit) {
            if (Server::where('client_id', $client->id)->where('plan_id', $plan->id)->where(function ($query) { $query->where('status', 0)->orWhere('status', 1); })->count() >= $plan->per_client_limit
                || !$category_model->verifyCategory($category, $client)) {
                return false;
            }
        }
        if (is_null($plan->global_limit)) return true;
        if (($servers = Server::where('plan_id', $plan->id)->where(function ($query) { $query->where('status', 0)->orWhere('status', 1); })->count()) < $plan->global_limit) {
            if (($available = $plan->global_limit - $servers) <= 10) {
                return $available;
            }
            return true;
        }
        return $category_model->verifyCategory($category);
    }

    public static function verifyPlanTrial(self $plan, Client $client)
    {
        return $plan->trial_length && $plan->trial_type && (is_null($plan->per_client_trial_limit) || Server::where('client_id', $client->id)->count() < $plan->per_client_trial_limit)
            && (new Category)->verifyCategoryTrial(Category::find($plan->category_id), $client);
    }
}
