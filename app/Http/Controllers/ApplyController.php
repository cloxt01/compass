<?php

namespace App\Http\Controllers;

use App\Clients\GlintsAPI;
use App\Clients\JobstreetAPI;
use App\Models\User;
use App\Services\Adapters\Provider\GlintsAdapter;
use App\Services\Adapters\Provider\JobstreetAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ApplyController extends Controller
{
    protected User $user;
    protected array $client;
    protected array $service;
    protected array $account;
    protected array $adapter;

    public function index()
    {
        $user = auth()->user();
        $adapters = [];
        $accounts = [];

        if ($user->jobstreetAccount && $user->jobstreetAccount->access_token) {
            $accounts['jobstreet'] = $user->jobstreetAccount;
            $adapters['jobstreet'] = new JobstreetAdapter(
                new JobstreetAPI($user->jobstreetAccount->access_token)
            );
        }
        if ($user->glintsAccount && $user->glintsAccount->cookie) {
            $accounts['glints'] = $user->glintsAccount;
            $adapters['glints'] = new GlintsAdapter(
                new GlintsAPI($user->glintsAccount->access_token, $user->glintsAccount->cookie)
            );
        }

        $next_run = DB::table('schedules')
            ->where('signature', 'app:apply-scheduler')
            ->value('next_run');
        return view('apply', compact('user', 'next_run', 'adapters', 'accounts'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'apply_configuration' => 'required|array',
            'apply_configuration.keyword' => 'required|string',
            'apply_configuration.batch' => 'required|integer|min:1',
            'apply_configuration.providers' => 'required|array',
        ]);


        $user = auth()->user();

        $user->apply_configuration = $request->input('apply_configuration');
        return redirect()->back()->with('success', 'Konfigurasi berhasil disimpan.');
    }

    /**
     * Stop automation for current user (semua provider)
     */
    public function stop(Request $request): RedirectResponse
    {

        try {
            $user = auth()->user();
            $user->automation_paused = true;
            $user->save();

            DB::table('jobs')
                ->where('payload', 'like', '%"user_id";i:' . $user->id . '%')
                ->delete();


            return redirect()->back()->with('success', 'Automation berhasil di non-aktifkan.');
        } catch (\Exception $e) {
            Log::error('apply@stop error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghentikan automation : '. $e->getMessage());
        }
    }

    /**
     * Resume (lanjutkan) automation for current user
     */
    public function resume(Request $request)
    {

        try {
            $user = auth()->user();

            $user->automation_paused = false;
            $user->save();

            return redirect()->back()->with('success', 'Automation berhasil di aktifkan.');
        } catch (\Exception $e) {
            Log::error('apply@resume error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melanjutkan automation : '. $e->getMessage());
        }
    }
}
