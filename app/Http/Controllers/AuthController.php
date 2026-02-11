<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller {
    public function login(Request $request) {

        
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        if(!Auth::attempt($credentials)){
            return back()->withErrors(['error' => 'Invalid credentials']);
        }
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard.index'));
    }
    public function register(Request $request) {
        $credentials = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'
        ]);

        $user = new User();
        $user->name = $credentials['name'];
        $user->email = $credentials['email'];
        $user->password = $credentials['password'];
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard.index'));
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();      
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}