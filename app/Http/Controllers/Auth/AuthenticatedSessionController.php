<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Cloudflare\CloudflareTurnstile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        if (!in_array(config('app.env'), ['local', 'testing'])) {
            $verified = CloudflareTurnstile::verify($request);

            if (!$verified) {
                return back()->withErrors([
                    'captcha' => 'Verifikasi keamanan gagal'
                ])->onlyInput('email');
            }
        }

        $request->authenticate();

        /*
        * User memiliki 2FA.
        * Jangan regenerate session atau redirect ke dashboard.
        * Arahkan ke Fortify Two-Factor Challenge.
        */
        if ($request->session()->has('login.id')) {
            return redirect()->route('two-factor.login');
        }

        /*
        * User tidak menggunakan 2FA.
        */
        $request->session()->regenerate();

        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
