<?php

namespace App\Http\Controllers\Api\Glints;

use App\Clients\GlintsAPI;
use App\Models\GlintsAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class api
{
    private \App\Services\Glints\API $service;

    public function __construct(protected GlintsAPI $client){
        $this->service = new \App\Services\Glints\API($client);
    }

    public function searchLocation(Request $request){
        $request->validate([
            'keyword' => 'string|required'
        ]);
        $response = $this->service->search_location($request->keyword);

        Log::info(json_encode($response));
        if($response['ok']){
            return response()->json($response, 200);
        }
        return response()->json([], 400);
    }
}
