<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class CreatePanelUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The client instance.
     *
     * @var \App\Models\Client
     */
    protected $client;
    protected $apiKey;
    protected $apiUrl;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
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
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get($this->apiUrl . '/api/application/users', [
            'filter[email]' => $this->client->email
        ]);

        if ($response->failed()) {
            Log::error("An error occurred while getting a panel user!");

            foreach ($response->json('errors', []) as $error) {
                Log::error($error['detail']);
            }

            return $this->fail();
        }

        $data = $response->json();
        if (!empty($data['data'])) {
            $user_obj = $data['data'][0];
            if ($user_obj['attributes']['email'] == $this->client->email
                && Client::where('user_id', $user_obj['attributes']['id'])->count() == 0) {
                $this->client->user_id = $user_obj['attributes']['id'];
                $this->client->save();
                return;
            }
        }

        $username = preg_replace("/[^A-Za-z0-9 ]/", '', strstr($this->client->email, '@', true) . Str::random(4));
        $create_response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->post($this->apiUrl . '/api/application/users', [
            'username' => $username,
            'email' => $this->client->email,
            'first_name' => 'First',
            'last_name' => 'Last',
        ]);

        if ($create_response->failed()) {
            Log::error("An error occurred while creating a panel user!");

            foreach ($create_response->json('errors', []) as $error) {
                Log::error($error['detail']);
            }

            return $this->fail();
        }

        $user_data = $create_response->json()['attributes'];
        $this->client->user_id = $user_data['id'];
        $this->client->save();
    }
}
