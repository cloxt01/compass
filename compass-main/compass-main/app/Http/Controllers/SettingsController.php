<?php

namespace App\Http\Controllers;

use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function settings(Request $request)
    {
        $tab = $request->query('tab', 'general');

        $allowedTabs = ['general', 'account', 'security', 'apply-configuration', 'ai-provider'];
        if (!in_array($tab, $allowedTabs)) {
            abort(404);
        }

        return view('settings.index', compact('tab'));
    }

    public function saveAiProvider(Request $request)
    {
        $data = $request->validate([
            'model' => ['nullable', 'string', 'max:200'],
            'api_key' => ['nullable', 'string', 'max:300'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
            'max_tokens' => ['nullable', 'integer', 'min:64', 'max:4096'],
            'clear_api_key' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $configuration = $user->apply_configuration ?? [];
        if (!is_array($configuration)) {
            $configuration = [];
        }
        $careerAi = is_array($configuration['career_ai'] ?? null) ? $configuration['career_ai'] : [];

        // Model
        $careerAi['model'] = isset($data['model']) && trim((string) $data['model']) !== ''
            ? trim((string) $data['model'])
            : (config('services.openrouter.model') ?: OpenRouterService::DEFAULT_MODEL);

        // API Key handling: only overwrite if provided; allow explicit clear
        if (!empty($data['clear_api_key'])) {
            unset($careerAi['api_key']);
        } elseif (isset($data['api_key']) && trim((string) $data['api_key']) !== '') {
            $careerAi['api_key'] = trim((string) $data['api_key']);
        }

        // Temperature
        $careerAi['temperature'] = isset($data['temperature'])
            ? max(0.0, min(2.0, (float) $data['temperature']))
            : OpenRouterService::DEFAULT_TEMPERATURE;

        // Max tokens
        $careerAi['max_tokens'] = isset($data['max_tokens'])
            ? max(64, min(4096, (int) $data['max_tokens']))
            : OpenRouterService::DEFAULT_MAX_TOKENS;

        $configuration['career_ai'] = $careerAi;
        $user->update(['apply_configuration' => $configuration]);

        return redirect()->route('settings', ['tab' => 'ai-provider'])
            ->with('success', 'Konfigurasi AI Provider berhasil disimpan.');
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
