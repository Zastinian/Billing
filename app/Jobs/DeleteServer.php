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

class DeleteServer implements ShouldQueue
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

        $server = Server::find($this->server_id);

        if (!$server) return;

        if ($server->status === 3) return;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->delete("{$this->apiUrl}/api/application/servers/{$this->server_id}");

        if ($response->failed()) {
            Log::error("An error occurred while deleting a server!");
            foreach ($response->json('errors', []) as $error) {
                Log::error($error['detail']);
            }
            return $this->fail();
        }

        $server->status = 3;
        $server->save();
    }
}
