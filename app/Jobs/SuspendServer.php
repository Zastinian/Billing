<?php

namespace App\Jobs;

use App\Models\Server;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuspendServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $server_id;
    protected $apiUrl;
    protected $apiKey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($server_id)
    {
        $this->server_id = $server_id;
        $this->apiKey = Setting::where('key', 'panel_app_api_key')->value('value');
        $this->apiUrl = Setting::where('key', 'panel_url')->value('value');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->server_id) return;

        $server = Server::where('server_id', $this->server_id)->first();

        if (!$server) return;

        if ($server->status === 2) return;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->post("{$this->apiUrl}/api/application/servers/{$this->server_id}/suspend");

        if ($response->failed()) {
            Log::error("An error occurred while suspending a server!");
            foreach ($response->json('errors', []) as $error) {
                Log::error($error['detail']);
            }
            return $this->fail();
        }

        $server->status = 2;
        $server->save();
    }
}
