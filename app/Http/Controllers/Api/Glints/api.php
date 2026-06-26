<?php

namespace App\Http\Controllers\Api\Glints;

use App\Clients\GlintsAPI;
use App\Models\GlintsAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class api
{
    private $provider;

    public function __construct(protected GlintsAPI $client){}

    public function searchLocation(Request $request){
        $request->validate([
            'keyword' => 'string|required'
        ]);
        $response = (new \App\Services\Glints\API($this->client))->search_location($request->keyword);

        Log::info(json_encode($response));
        if($response['ok']){
            return response()->json($response, 200);
        }
        return response()->json([], 400);
    }
}
