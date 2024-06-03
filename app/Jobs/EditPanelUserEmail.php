<?php

namespace App\Jobs;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EditPanelUserEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $email;
    protected $apiUrl;
    protected $apiKey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $email)
    {
        $this->user_id = $user_id;
        $this->email = $email;
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
        ])->get("{$this->apiUrl}/api/application/users/{$this->user_id}");

        if ($response->failed()) {
            Log::error("An error occurred while getting the panel user!");

            foreach ($response->json('errors', []) as $error) {
                Log::error($error['detail']);
            }
            return $this->fail();
        }

        $user = $response->json('attributes');

        $updateResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->patch("{$this->apiUrl}/api/application/users/{$this->user_id}", [
            'email' => $this->email,
            'username' => $user['username'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'language' => $user['language'],
        ]);

        if ($updateResponse->failed()) {
            Log::error("An error occurred while updating the panel email!");

            foreach ($updateResponse->json('errors', []) as $error) {
                Log::error($error['detail']);
            }

            return $this->fail();
        }
    }
}
