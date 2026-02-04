<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

use App\Jobs\ProcessConnectAccount;
use App\Clients\Token;


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
            $payload = json_encode([
                    'id' => Str::uuid()->toString(),
                    'name' => 'send-otp',
                    'data' => [
                        'user_id' => $user_id,
                        'provider' => $provider,
                        'email' => $email,
                        'uuid' => $uuid,
                    ],
                ], JSON_UNESCAPED_SLASHES
                );
            Log::info(gettype($payload));
            Log::info( $payload);
            Redis::connection()->rpush(
                ('bull:compass-queue:wait'),
                 $payload
                
            );

            return response()->json(['status' => 'started'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }


    public function verify_otp(Request $request, $provider){
        try {
            $request->validate([
                'email' => 'required|email',
                'code' => 'required|string|max:20',
                'uuid' => 'required|string|max:255',
                'user_id' => 'required|integer'
            ]);

            $client = new Token();
            $response = $client->verify_otp($request, $provider);
            $isJson = $response->headers()['Content-Type'] === 'application/json';
            
            if (!$isJson || isset($response['error'])) {
                return response()->json(['status' => 'error', 'errors' => ['error' => $response['error'], 'message' => $response['error_description']]], 400);
            }

            Redis::connection()->rpush(
                ('bull:compass-queue:wait'),
                 json_encode([
                    'id' => Str::uuid()->toString(),
                    'name' => 'verify-otp',
                    'data' => [
                        'user_id' => $request->input('user_id'),
                        'provider' => $provider,
                        'email' => $request->input('email'),
                        'uuid' => $request->input('uuid')
                    ],
                ], JSON_UNESCAPED_SLASHES
                )
            );
            return response()->json(['status' => 'success', 'data' => $response], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw ValidationException::withMessages($e->errors());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'An error occurred', 'errors' => $e->getMessage()], 500);
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
                ['user_id' => $user->id], // filter
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