<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use \App\Jobs\ProcessAuthentication;


class JobstreetService
{
    public function login(Request $request)
    {
        $email = $request->input('email');

        try {
            ProcessAuthentication::dispatch(Str::uuid()->toString(), $email)->onQueue('authentication_queue');
            Log::info("Login started for email: $email");
            return ['status' => true, 'message' => 'Proses autentikasi dimulai.', 'otp_required' => true];
        } catch (\Exception $e) {
            Log::error(json_encode(['status' => false, 'message' => $e->getMessage()]));
            return response()->json(['status' => false, 'message' => 'Gagal memulai proses autentikasi.']);
        }
    }

    public function submit_otp(Request $request)
    {
        $otp = $request->input('otp');


        $send = file_put_contents(base_path('resources/js/login-automation/otp.json'), json_encode(['otp' => $otp]));

        Log::info($send ? "OTP dikirim : " . $otp: "Gagal mengirim OTP.");

        return response()->json(['status' => $send ? true : false, 'message' => $send ? 'OTP dikirim' : 'Gagal mengirim OTP.']);
    }

    public function show_otpForm(){
        return view('auth.verification_otp');
    }

}