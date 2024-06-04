<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportPanelUsers implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pages;
    protected $apiUrl;
    protected $apiKey;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->pages = 1;
        $this->apiKey = Setting::where('key', 'panel_app_api_key')->value('value');
        $this->apiUrl = Setting::where('key', 'panel_url')->value('value');
        $this->importUsers();

        for ($p = 2; $p <= $this->pages; $p++) {
            $this->importUsers($p);
        }
    }

    private function importUsers(int $page = 1)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get($this->apiUrl . '/api/application/users?page=' . $page);

        if ($response->failed()) {
            Log::error('An error occurred while getting users from Pterodactyl API!');
            foreach ($api->json()['errors'] as $error) {
                Log::error($error);
            }
        } else {
            $data = $response->json();
            $this->pages = $data['meta']['pagination']['total_pages'];
            foreach ($data['data'] as $user_obj) {
                $email = $user_obj['attributes']['email'];
                $user_id = $user_obj['attributes']['id'];

                if (!Client::where('email', $email)->orWhere('user_id', $user_id)->first()) {
                    Client::create([
                        'email' => $email,
                        'user_id' => $user_id,
                        'password' => Hash::make(Str::random(16)),
                    ]);
                }
            }
        }
    }
}
