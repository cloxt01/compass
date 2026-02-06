<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

use App\Models\User;
use App\Services\JobstreetToken;
use App\Exceptions\UnknownOperation;
use App\Exceptions\UnknownProvider;



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
            return response()->json(['status' => 'failed', 'errors' => ['server' => ['redis connection failed']]], 500);
        }
        return response()->json(['status' => 'started'], 200);
    }

    public function verify_otp(Request $request, $provider){
        try {
            $request->validate([
                'email' => 'required|email',
                'verification_code' => 'required|string|max:20',
                'request_id' => 'required|string|max:255'
            ]);
            $client = match($provider) {
                'jobstreet' => new JobstreetToken,
                // Other providers
                default => throw new UnknownProvider($provider)
            };
            
            $is_verified = $client->verify_otp($request->input('email'), $request->input('verification_code'));
            if (!$is_verified) {
                return response()->json(['status' => 'failed', 'data' => 'Invalid OTP'], 200);
            }
            $success = Redis::connection()->hset(("otp:". $request->input('request_id')), "otp", $request->input('verification_code'));
            if(!$success){
                throw new Exception("Gagal kirim ke redis");
            }

            return response()->json(['status' => 'success', 'data' => 'OK'], 200);
        } catch(\UnknownProvider $e){
            return response()->json(['status' => 'failed', 'errors' => ['provider' =>[$e->getMessage()]]], 400);
        } catch(\Exception $e){
            return response()->json(['status' => 'failed', 'errors' => ['server' => [$e->getMessage()]]], 500);
        }
        
    }

    public function disconnect(Request $request, $provider){
        Log::info("Disconnecting from external platform: " . $provider);

        try {
            $user = auth()->user();
            if ($user->jobstreetAccount) {
                $user->jobstreetAccount()->delete();
                $user->refresh();
                return redirect()->route('external.index');
            }
            return response()->json(['success' => false, 'message' => 'Account not found'], 404);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to disconnect account', 'errors' => $e->getMessage()], 500);
        } 
    }

    public function save_token(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|json'
            ]);
            $token = json_decode($request->input('token'), true);

            if (!isset($token['access_token'], $token['refresh_token'], $token['expires_in'])) {
                return response()->json(['status' => 'failed', 'errors' => ['token' =>['Invalid token format']]], 400);
            }
            $user = User::find(auth()->user()->id);
            Log::info($user);

            $add = $user->jobstreetAccount->updateToken($token);
            Log::info("Status add token: " .$add);

            return response()->json([
                'redirect' => route('external.index')
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'errors' => ['server' => [$e->getMessage()]]], 500);
        }
    }
}