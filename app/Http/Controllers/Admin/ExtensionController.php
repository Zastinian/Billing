<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiController;
use Extensions\ExtensionManager;
use Illuminate\Http\Request;

class ExtensionController extends ApiController
{
    public function show($id)
    {
        if (is_null($extension = ExtensionManager::getExtension($id)))
            return abort(404);
            
        return $extension::show();
    }

    public function store(Request $request, $id)
    {
        if (is_null($extension = ExtensionManager::getExtension($id)))
            return abort(404);

        return $this->respondJson($extension::store($request));
    }
}
