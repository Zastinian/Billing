<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\ApiController;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubdomainController extends ApiController
{
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $data = explode(':', $request->input('subdomain'));
        
        if (($result = $data[0]::updateSubdomain($request->input('name'), $data[1], Server::find($id)->subdomain_port)) === true) {
            return $this->respondJson(['success' => 'Your server subdomain has been updated successfully!']);
        } else {
            return $this->respondJson(['error' => $result]);
        }
    }
}
