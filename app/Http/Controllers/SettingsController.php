<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function settings(Request $request)
    {
        $tab = $request->query('tab', 'general');

        $allowedTabs = ['general', 'account', 'security', 'apply-configuration'];
        if (!in_array($tab, $allowedTabs)) {
            abort(404);
        }

        return view('settings.index', compact('tab'));
    }
    public function upsert_user(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:5'],
        ]);

        if($user->email != $validated['email']) {
            $user->email_verified_at = null;
        }
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function toggle_automation(Request $request)
    {
        $request->validate([
            'automation_paused' => ['required', 'boolean']
        ]);
        $user = auth()->user();

        $user->automation_paused = $request->automation_paused;
        $user->save();

        return response()->json([
            'success' => true,
            'automation_paused' => (bool) $user->automation_paused,
        ]);
    }
}
