<?php

namespace App\Jobs;

use App\Models\Addon;
use App\Models\Plan;
use App\Models\Server;
use App\Models\Setting;
use App\Models\ServerAddon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class UpdateServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function handle()
    {
        $apiKey = Setting::where('key', 'panel_app_api_key')->value('value');
        $apiUrl = Setting::where('key', 'panel_url')->value('value');

        $plan = Plan::find($this->server->plan_id);
        $cpu = $plan->cpu;
        $ram = $plan->ram;
        $swap = $plan->swap;
        $disk = $plan->disk;
        $io = $plan->io;
        $databases = $plan->databases;
        $backups = $plan->backups;
        $extra_ports = $plan->extra_ports;

        foreach (ServerAddon::where('server_id', $this->server->id) as $server_addon) {
            $addon = Addon::find($server_addon->addon_id);
            switch ($addon->resource) {
                case 'ram':
                    $ram += $addon->amount * $server_addon->quantity;
                    break;
                case 'cpu':
                    $cpu += $addon->amount * $server_addon->quantity;
                    break;
                case 'disk':
                    $disk += $addon->amount * $server_addon->quantity;
                    break;
                case 'database':
                    $databases += $addon->amount * $server_addon->quantity;
                    break;
                case 'backup':
                    $backups += $addon->amount * $server_addon->quantity;
                    break;
                case 'extra_port':
                    $extra_ports += $addon->amount * $server_addon->quantity;
                    break;
                case 'dedicated_ip':
                    $allocation_id = $addon->amount;
                    break;
            }
        }

        $buildData = [
            'cpu' => $cpu,
            'memory' => $ram,
            'swap' => $swap,
            'disk' => $disk,
            'io' => $io,
            'feature_limits' => [
                'databases' => $databases,
                'backups' => $backups,
                'allocations' => $extra_ports + 1,
            ],
        ];

        if (isset($allocation_id)) {
            $buildData['allocation'] = $allocation_id;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ])->patch("{$apiUrl}/api/application/servers/{$this->server->id}/build", $buildData);

        if ($response->failed()) {
            Log::error("An error occurred while updating server build details!");
            Log::error($response->body());
            return $this->fail();
        }

        $eggResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ])->get("{$apiUrl}/api/application/nests/{$this->server->nest_id}/eggs/{$this->server->egg_id}?include=variables");

        if ($eggResponse->failed()) {
            Log::error("An error occurred while getting egg details!");
            Log::error($eggResponse->body());
            return $this->fail();
        }

        $egg = $eggResponse->json();
        $startupData = [
            'startup' => $egg['attributes']['startup'],
            'environment' => $egg['attributes']['relationships']['variables']['data'],
            'egg' => $this->server->egg_id,
            'image' => $egg['attributes']['docker_image'],
            'skip_scripts' => true,
        ];

        $startupResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ])->patch("{$apiUrl}/api/application/servers/{$this->server->id}/startup", $startupData);

        if ($startupResponse->failed()) {
            Log::error("An error occurred while updating server startup details!");
            Log::error($startupResponse->body());
            return $this->fail();
        }

        $this->server->status = 0;
        $this->server->save();
    }
}
