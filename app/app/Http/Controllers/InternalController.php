<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class InternalController extends Controller {
    

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

    public function update_login_status(Request $request)
    {
        $user_id = $request->input('user_id');
        $uuid = $request->input('uuid');
        $status = $request->input('status');
        $data = $request->input('data');

        if($status === 'done'){
            $del = DB::table('login_status')->where('uuid', $uuid)->where('user_id', $user_id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Login completed']);
        } 
        else if($status === 'failed'){
            $dd = DB::table('login_status')->updateOrInsert(
                ['uuid' => $uuid, 'user_id' => $user_id],
                [
                    'status' => $status,
                    'exception' => $data['error'] ?? null
                ]
            );
            if($dd){
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error'], 500);
            }
        }
        return response()->json(['status' => 'unknown', 'message' => 'Unknown status']);
    }

    public function check_login_otp(Request $request)
    {
        try {
            $request->validate([
                'uuid' => 'required|string',
                'user_id' => 'nullable|integer'
            ]);

            $query = DB::table('otps')
                ->where('uuid', $request->uuid)
                ->where('consumed', 0);

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $otp = $query->value('otp');

            return response()->json([
                'success' => (bool) $otp,
                'otp' => $otp
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update_otp_consumed(Request $request)
    {
        try {
            $request->validate([
                'uuid' => 'required|string',
                'user_id' => 'nullable|integer'
            ]);

            $query = DB::table('otps')
                ->where('uuid', $request->uuid)
                ->where('consumed', 0);

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $updated = $query->update(['consumed' => 1]);

            return response()->json([
                'success' => $updated > 0
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }    

    public function send_login_otp(Request $request)
    {
        // Manual validator to ensure JSON error response and accept unauthenticated clients
        $validator = $request->validate([
            'uuid' => 'required|string',
            'otp' => 'required|string|max:8',
            'user_id' => 'required|integer'
        ]);

        $uuid = $request->input('uuid');
        $otp = $request->input('otp');
        $user_id = $request->input('user_id', null);

        try {
            $payload = ['user_id' => $user_id, 'uuid' => $uuid, 'otp' => $otp];
            $ok = DB::table('otps')->insert($payload);
            if (! $ok) {
                return response()->json(['status' => 'error', 'message' => 'Insert failed'], 500);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}