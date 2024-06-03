<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\ApiController;
use App\Models\Server;
use App\Models\Setting;
use Extensions\ExtensionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SoftwareController extends ApiController
{
    public function update(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'software' => 'required|string',
            'version' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->respondJson(['errors' => $validator->errors()->all()]);
        }

        $server = Server::find($id);
        if (is_null($server)) {
            return $this->respondJson(['error' => 'Server not found!']);
        }

        $apiKey = Setting::where('key', 'panel_client_api_key')->value('value');
        $apiUrl = Setting::where('key', 'panel_url')->value('value');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ])->get("{$apiUrl}/api/client/servers/{$server->identifier}/files/upload");

        if ($response->successful() && empty($response->json('errors'))) {
            $data = explode(':', $request->input('software'));

            $extension = ExtensionManager::getExtension($data[0]);
            if (is_null($extension)) {
                return $this->respondJson(['error' => 'Software does not exist!']);
            }

            $file = $extension::install($data[1], $request->input('version'));
            $filePath = base_path('extensions/Softwares/' . $extension::$display_name . '/softwares/' . $file);

            if (!file_exists($filePath)) {
                return $this->respondJson(['error' => 'File does not exist!']);
            }

            $uploadResponse = Http::attach(
                'file', file_get_contents($filePath), $file
            )->post($response->json('attributes.url'));

            if ($uploadResponse->successful()) {
                return $this->respondJson(['success' => 'The server software has been uploaded to your server.']);
            }
        }

        return $this->respondJson(['error' => 'Failed to upload the server software!']);
    }
}
