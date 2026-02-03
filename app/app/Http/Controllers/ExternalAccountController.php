<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use App\Jobs\ProcessConnectAccount;



class ExternalAccountController extends Controller {

    public function send_otp(Request $request, $provider){
        Log::info("Sending OTP to external platform: " . $provider);

        try{
            $request->validate([
                'uuid' => 'required|string|max:255',
                'email' => 'required|email'
            ]);
            $user_id = auth()->user()->id;
            $uuid = $request->input('uuid');
            $email = $request->input('email');
            
            Redis::connection()->rpush(
                ('bull:compass-queue:wait'),
                 json_encode([
                    'id' => Str::uuid()->toString(),
                    'name' => 'send-otp',
                    'data' => [
                        'user_id' => $user_id,
                        'provider' => $provider,
                        'email' => $email,
                        'uuid' => $uuid,
                    ],
                ], JSON_UNESCAPED_SLASHES
                )
                
            );

            return response()->json(['status' => 'started'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }

    public function verify_otp(Request $request, $provider){
    }

    public function disconnect(Request $request, $provider){
        Log::info("Disconnecting from external platform: " . $provider);

        try {
            $user = auth()->user();
            if ($user->jobstreetAccount) {
                $user->jobstreetAccount()->delete();
                $user->refresh();
                return response()->json(['success' => true, 'message' => 'Disconnected from ' . $provider], 200);
            }
            return response()->json(['success' => false, 'message' => 'Account not found'], 404);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to disconnect account', 'errors' => $e->getMessage()], 500);
        } 
    }
}