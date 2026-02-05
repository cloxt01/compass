<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

use App\Clients\JobstreetToken;
use App\Exceptions\UnknownOperation;



class ExternalAccountController extends Controller {
    function __construct(){
    }

    public function passwordless_login(Request $request, $provider){
        $request->validate([
        'request_id' => 'required|string|max:255',
        'email' => 'required|email'
        ]);
        $user_id = auth()->user()->id;
        $success = Redis::connection()->rpush(
            ('bull:compass-queue:wait'),
                json_encode([
                    'request_id' => $request->input('request_id'),
                    'operation' => 'passwordless-login',
                    'data' => [
                        'user_id' => $user_id,
                        'provider' => $provider,
                        'email' => $request->input('email')
                    ],
                ], JSON_UNESCAPED_SLASHES
            )
        );
        if(!$success){
            return response()->json(['status' => 'failed', 'message' => 'Gagal kirim ke redis'], 500);
        }
        return response()->json(['status' => 'started'], 200);
        
        

    }

    public function verify_otp(Request $request, $provider){
        try {
            $request->validate([
                'verification_code' => 'required|string|max:20',
                'request_id' => 'required|string|max:255'
            ]);
            $client = match($provider) {
                'jobstreet' => new JobstreetToken,
                default => throw new UnknownOperation("Provider not supported: " . $provider)
            };
            
            $is_verified = $client->token_client->verify_otp($request, $provider);
            if ($is_verified) {
                return response()->json(['status' => 'failed', 'data' => 'Invalid OTP'], 200);
            }
            $success = Redis::connection()->hset(("otp:". $request->input('request_id')), "otp", $request->input('verification_code'));
            if(!$success){
                throw new Exception("Gagal kirim ke redis");
            }

            return response()->json(['status' => 'success', 'data' => (string) $response->body()], 200);
        } catch(\Exception $e){
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }
        
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

    public function add_token(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $token = $request->input('token');

            if (!$user_id || !isset($token['access_token'], $token['refresh_token'], $token['expires_in'])) {
                throw new \Exception("Invalid JSON format for token.");
            }

            $user = User::find($user_id);
            if (!$user) throw new \Exception("User not found");

            $user->jobstreetAccount()->updateOrCreate(
                ['user_id' => $user->id], 
                [
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'],
                    'expires_at' => now()->addSeconds($token['expires_in']),
                    'status' => 'active'
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Token added successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}