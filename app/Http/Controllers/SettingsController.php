<?php

namespace App\Http\Controllers;

use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function settings(Request $request)
    {
        $tab = $request->query('tab', 'general');
        $user = Auth()->user(); 

        $allowedTabs = ['general', 'security', 'apply-configuration', 'ai-provider', 'ai-profile', 'profile'];
        if (!in_array($tab, $allowedTabs)) {
            abort(404);
        }

        return view('settings.index', compact('tab', 'user'));
    }

    public function saveAiProvider(Request $request)
    {
        $data = $request->validate([
            'model' => ['nullable', 'string', 'max:200'],
            'api_key' => ['nullable', 'string', 'max:300'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
            'max_tokens' => ['nullable', 'integer', 'min:64', 'max:8192'],
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

    public function testAiProvider(Request $request, OpenRouterService $openRouter)
    {
        $data = $request->validate([
            'model' => ['nullable', 'string', 'max:200'],
            'api_key' => ['nullable', 'string', 'max:300'],
        ]);

        $result = $openRouter->testConnection([
            'model' => $data['model'] ?? null,
            'api_key' => $data['api_key'] ?? null,
        ], $request->user());

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    public function saveAiProfile(Request $request)
    {
        $data = $request->validate([
            'nama' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'kewarganegaraan' => ['nullable', 'in:Indonesia,Lainnya'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:Laki-laki,Perempuan'],

            'pendidikan' => ['nullable', 'in:SMA/SMK,Diploma,S1,S2,S3'],
            'jurusan' => ['nullable', 'string', 'max:150'],
            'institusi' => ['nullable', 'string', 'max:200'],
            'tahun_lulus' => ['nullable', 'integer', 'min:1970', 'max:2035'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],

            'pengalaman_level' => ['nullable', 'in:FRESH_GRADUATE,LESS_THAN_ONE_YEAR,ONE_TO_THREE_YEARS,THREE_TO_FIVE_YEARS,FIVE_TO_TEN_YEARS,GREATER_THAN_TEN_YEARS'],
            'pengalaman_tahun' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'posisi_terakhir' => ['nullable', 'string', 'max:150'],
            'perusahaan_terakhir' => ['nullable', 'string', 'max:200'],

            'skills' => ['nullable', 'array', 'max:50'],
            'skills.*.name' => ['nullable', 'string', 'max:100'],
            'skills.*.level' => ['nullable', 'in:NO_EXPERIENCE,BASIC,INTERMEDIATE,ADVANCED'],

            'sertifikasi' => ['nullable', 'array', 'max:50'],
            'sertifikasi.*.nama' => ['nullable', 'string', 'max:150'],
            'sertifikasi.*.issuer' => ['nullable', 'string', 'max:150'],
            'sertifikasi.*.tahun' => ['nullable', 'integer', 'min:1970', 'max:2035'],

            'bahasa' => ['nullable', 'array', 'max:20'],
            'bahasa.*.nama' => ['nullable', 'string', 'max:80'],
            'bahasa.*.level' => ['nullable', 'in:BASIC,INTERMEDIATE,FLUENT,NATIVE'],

            'gaji_terakhir' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'ekspektasi_gaji' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'notice_period' => ['nullable', 'in:IMMEDIATELY,TWO_WEEKS,ONE_MONTH,TWO_MONTHS'],

            'bersedia_wfo' => ['nullable', 'boolean'],
            'bersedia_wfh' => ['nullable', 'boolean'],
            'bersedia_hybrid' => ['nullable', 'boolean'],
            'bersedia_luar_kota' => ['nullable', 'boolean'],
            'bersedia_industri_banking' => ['nullable', 'boolean'],

            'catatan' => ['nullable', 'string', 'max:5000'],
        ]);

        // Bersihkan array dari row kosong
        $cleanRows = function ($rows, array $requiredKeys) {
            if (!is_array($rows)) return [];
            $out = [];
            foreach ($rows as $r) {
                if (!is_array($r)) continue;
                $filled = false;
                foreach ($requiredKeys as $k) {
                    if (isset($r[$k]) && trim((string) $r[$k]) !== '') { $filled = true; break; }
                }
                if ($filled) $out[] = $r;
            }
            return $out;
        };

        $profile = [
            'nama' => $data['nama'] ?? null,
            'phone' => $data['phone'] ?? null,
            'lokasi' => $data['lokasi'] ?? null,
            'kewarganegaraan' => $data['kewarganegaraan'] ?? null,
            'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
            'gender' => $data['gender'] ?? null,

            'pendidikan' => $data['pendidikan'] ?? null,
            'jurusan' => $data['jurusan'] ?? null,
            'institusi' => $data['institusi'] ?? null,
            'tahun_lulus' => isset($data['tahun_lulus']) ? (int) $data['tahun_lulus'] : null,
            'ipk' => isset($data['ipk']) ? (float) $data['ipk'] : null,

            'pengalaman_level' => $data['pengalaman_level'] ?? null,
            'pengalaman_tahun' => isset($data['pengalaman_tahun']) ? (float) $data['pengalaman_tahun'] : null,
            'posisi_terakhir' => $data['posisi_terakhir'] ?? null,
            'perusahaan_terakhir' => $data['perusahaan_terakhir'] ?? null,

            'skills' => $cleanRows($data['skills'] ?? [], ['name']),
            'sertifikasi' => $cleanRows($data['sertifikasi'] ?? [], ['nama']),
            'bahasa' => $cleanRows($data['bahasa'] ?? [], ['nama']),

            'gaji_terakhir' => isset($data['gaji_terakhir']) ? (int) $data['gaji_terakhir'] : null,
            'ekspektasi_gaji' => isset($data['ekspektasi_gaji']) ? (int) $data['ekspektasi_gaji'] : null,
            'notice_period' => $data['notice_period'] ?? null,

            'bersedia_wfo' => (bool) ($data['bersedia_wfo'] ?? false),
            'bersedia_wfh' => (bool) ($data['bersedia_wfh'] ?? false),
            'bersedia_hybrid' => (bool) ($data['bersedia_hybrid'] ?? false),
            'bersedia_luar_kota' => (bool) ($data['bersedia_luar_kota'] ?? false),
            'bersedia_industri_banking' => (bool) ($data['bersedia_industri_banking'] ?? false),

            'catatan' => $data['catatan'] ?? null,
        ];

        $user = $request->user();
        $configuration = is_array($user->apply_configuration) ? $user->apply_configuration : [];
        $autoAnswer = is_array($configuration['auto_answer'] ?? null) ? $configuration['auto_answer'] : [];
        $autoAnswer['profile'] = $profile;
        $configuration['auto_answer'] = $autoAnswer;
        $user->update(['apply_configuration' => $configuration]);

        return redirect()->route('settings', ['tab' => 'ai-profile'])
            ->with('success', 'Profil kandidat berhasil disimpan.');
    }

    public function destroy_user(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ], 'userDeletion');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
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
