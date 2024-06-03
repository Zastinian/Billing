<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class Pterodactyl
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = Setting::where('key', 'panel_app_api_key')->value('value');
        $this->baseUrl = Setting::where('key', 'panel_url')->value('value');
    }

    private function getPaginatedData($endpoint)
    {
        $data = [];
        $page = 1;
        $totalPages = 1;

        while ($page <= $totalPages) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . $endpoint . '&page=' . $page);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch data from Pterodactyl API');
            }

            $body = $response->json();
            $data = array_merge($data, $body['data']);
            $meta = $body['meta']['pagination'];
            $totalPages = $meta['total_pages'];
            $page++;
        }

        return [
            'data' => $data,
            'meta' => [
                'pagination' => [
                    'total' => count($data),
                    'count' => count($data),
                    'per_page' => $meta['per_page'],
                    'current_page' => 1,
                    'total_pages' => $totalPages,
                ],
            ],
        ];
    }

    public function getLocations()
    {
        return $this->getPaginatedData('/api/application/locations?include=nodes');
    }

    public function getNests()
    {
        return $this->getPaginatedData('/api/application/nests?include=eggs');
    }
}
