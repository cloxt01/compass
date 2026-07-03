<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

use App\Models\User;
use App\Services\Token\JobstreetToken;
use App\Services\Token\GlintsToken;

use App\Exceptions\UnknownOperation;
use App\Exceptions\UnknownProvider;



class ConnectionController extends Controller {
    function __construct(){
        // $this->redis = Redis::connection();
        // if(!$this->redis->ping())
        // return response()->json(['status' => 'failed', 'errors' => ['redis' => ['Redis connection failed']]], 500);;
    }


    public function passwordless_login(Request $request, $provider){
        $request->validate([
        'request_id' => 'required|string|max:255',
        'email' => 'required|email',
        'user_id' => 'required|integer|exists:users,id'
        ]);
        $user_id = $request->input('user_id');
        $success = $this->redis->rpush(
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
                // Provider lainn
                default => throw new UnknownProvider($provider)
            };

            $is_verified = $client->verify_otp($request->input('email'), $request->input('verification_code'));

            switch($is_verified){
                case 'blocked':
                    return response()->json(['status' => 'failed', 'errors' => [$provider.'_server' => ['Too many requests, please unblock your account in email inbox']]], 429);
                case 'unverified':
                    return response()->json(['status' => 'failed', 'data' => 'Invalid OTP'], 200);
                case 'failed':
                    return response()->json(['status' => 'failed', 'errors' => ['server' => ['Server error, please try again later.']]], 500);
                case 'verified':
                    break;
            }
            $this->redis->hset(("otp:". $request->input('request_id')), "otp", $request->input('verification_code'));
            $this->redis->expire("otp:". $request->input('request_id'), 3600);


            return response()->json(['status' => 'success', 'data' => 'OK'], 200);
        } catch(\UnknownProvider $e){
            return response()->json(['status' => 'failed', 'errors' => ['provider' =>[$e->getMessage()]]], 400);
        } catch(\Exception $e){
            return response()->json(['status' => 'failed', 'errors' => ['server' => [$e->getMessage()]]], 500);
        }

    }

    public function disconnect(Request $request, $provider){
        Log::info("Disconnecting from external connection: " . $provider);

        try {

            $user = auth()->user();
            $account = match($provider) {
                'jobstreet' => $user->jobstreetAccount,
                'glints' => $user->glintsAccount,
                default => throw new UnknownProvider($provider)
            };

            $account->delete();
            $user->refresh();
            return redirect()->route('connection.'.$provider);
//            return response()->json(['success' => false, 'message' => 'Account not found'], 404);

        } catch(\UnknownProvider $e){
            return response()->json(['status' => 'failed', 'errors' => ['provider' =>[$e->getMessage()]]], 400);
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
            $add = $user->jobstreetAccount()->updateOrCreate(
                ['user_id' => $user->id],
                $token
            );

            return response()->json([
                'redirect' => route('settings')
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'errors' => ['server' => [$e->getMessage()]]], 500);
        }
    }

    public function save_config(Request $request, $provider){
        try {
            $user = auth()->user();
            $account = match($provider){
                'jobstreet' => $user->jobstreetAccount,
                'glints' => $user->glintsAccount,
                default => throw new UnknownProvider($provider)
            };

            foreach ($request->except('_token') as $key => $value) {
                if (is_numeric($value)) {
                    $value = (int) $value;
                }

                $account->saveConfig($key, $value);
            }

            if(!$account){
                return response()->json(['status' => 'failed', 'errors' => ['account' => ['Account not found']]], 404);
            }
            return back()->with('success', 'Configuration updated!');
        } catch (UnknownProvider $e){
            return response()->json(['status' => 'failed', 'errors' => ['provider' => [$e->getMessage()]]], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'errors' => ['server' => [$e->getMessage()]]], 500);
        }
    }

    public function login(Request $request, $provider){

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        Log::info("Logining from external connection: " . $provider);

        $user = User::find($request->input('user_id'));
        if(!$user) {
            return response()->json(['status' => 'failed', 'errors' => ['user' => ['User not found']]], 404);
        }

        $client = match($provider) {
//            'jobstreet' => new JobstreetToken,
            'glints' => new GlintsToken,
            default => throw new UnknownProvider($provider)
        };

        $data = $client->getToken($request->input('email'), $request->input('password'));
        Log::info("Logining from external connection: " . $provider);
        Log::info("Output from getToken : ".json_encode($data));
        if(!$data) {
            return response()->json(['status' => 'failed', 'errors' => ['token' => ['Email atau password salah']]], 400);
        }
        Log::info("Token generated : " . $data['access_token']);
        Log::info("Cookie generated : " . $data['cookie']);
        $saved = $user->glintsAccount()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'access_token' => $data['access_token'],
                'cookie' => $data['cookie'],
                'status' => 'active',
                'updated_at' => now(),
            ]
        );


        if($saved){
            return redirect()->route('connection.'.$provider)->with('success', 'Login berhasil');
        }
        return back()->with('error', 'Failed to save token');
    }
}
