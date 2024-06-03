<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function __construct(Request $request)
    {
//        if (!$request->accepts(['application/json']) || !$request->expectsJson())
//            throw new Exception('The accept header is not JSON.', 400);
    }

    protected function respondJson($data, $status = 200)
    {
        return response()->json($data, $status, ['Content-Type' => 'application/json']);
    }
}
