<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $fillable = [
        'name',
        'description',
        'resource',
        'amount',
        'categories',
        'global_limit',
        'per_client_limit',
        'per_server_limit',
        'order',
    ];

    public static function verifyAddon(self $addon, Client $client = null)
    {
        if ($client && $addon->per_client_limit) {
            $addons = 0;
            foreach (Server::where('client_id', $client->id)->where(function ($query) { $query->where('status', 0)->orWhere('status', 1); })->get() as $server) {
                if ($quantity = ServerAddon::where('server_id', $server->id)->where('addon_id', $addon->id)->value('quantity')) $addons += $quantity;
            }
            if ($addons >= $addon->per_client_limit) {
                return false;
            }
        }
        
        if (is_null($addon->global_limit)) return true;

        $addons = 0;
        foreach (Server::where('status', 0)->orWhere('status', 1)->get() as $server) {
            if ($quantity = ServerAddon::where('server_id', $server->id)->where('addon_id', $addon->id)->value('quantity')) $addons += $quantity;
        }
        return $addons < $addon->global_limit;
    }

    public static function dediIpList()
    {
        $ips = [];
        foreach (self::where('resource', 'dedicated_ip')->get() as $addon)
            foreach (explode(',', $addon->amount) as $ip)
                array_push($ips, $ip);
        
        return $ips;
    }
}
