<?php

namespace App\Http\Controllers;

use App\Services\Adapters\GlintsAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;

use App\Models\User;
use App\Models\JobstreetAccount;

use App\Clients\JobstreetAPI;
use App\Clients\GlintsAPI;
use App\Services\Adapters\JobstreetAdapter;

use App\Jobs\ProcessApplications;

use App\Exceptions\UnknownProvider;
use App\Exception\AccountNotFound;

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

        return view('apply', compact('user', 'adapters', 'accounts'));
    }

    /**
     * Push (Start) new automation jobs
     */
    public function push(Request $request)
    {
        Log::info('apply@push DIPANGGIL');

        try {
            $request->validate([
                'providers' => 'required|array|min:1',
                'providers.*' => 'in:jobstreet,glints',
                'keyword' => 'required|string',
                'pageSize' => 'required|integer|min:1|max:40',
            ]);

            $this->user = auth()->user();

            // ✅ CEK PAUSE PER USER (bukan per provider)
            if ($this->user->automation_paused) {
                return redirect()->back()->withErrors([
                    'msg' => 'Automation is paused. Please resume first.'
                ]);
            }

            foreach ($request->input('providers') as $provider) {
                // 1. Ambil akun
                $this->account[$provider] = match ($provider) {
                    'jobstreet' => $this->user->jobstreetAccount,
                    'glints'    => $this->user->glintsAccount,
                    default     => throw new UnknownProvider($provider)
                };

                if (!$this->account[$provider]) {
                    throw new AccountNotFound("$provider account not found");
                }

                // 2. Inisialisasi klien
                $this->client[$provider] = match ($provider) {
                    'jobstreet' => new JobstreetAPI($this->user->jobstreetAccount?->access_token),
                    'glints'    => new GlintsAPI($this->user->glintsAccount->access_token, $this->user->glintsAccount->cookie),
                };

                // 3. Inisialisasi adapter
                $this->adapter[$provider] = match ($provider) {
                    'jobstreet' => new JobstreetAdapter($this->client[$provider]),
                    'glints'    => new GlintsAdapter($this->client[$provider]),
                };

                // 4. Cek status koneksi
                if ($this->account[$provider]->status === 'reauth_required') {
                    return redirect()
                        ->route("platform.disconnect", ['provider' => $provider])
                        ->withErrors(['msg' => "Koneksi ke $provider terputus, silakan hubungkan ulang."]);
                }

                // 5. Bangun parameter pencarian
                $params = match ($provider) {
                    'jobstreet' => [
                        'keyword'  => $request->input('keyword'),
                        'location' => (string) $this->account[$provider]->getConfig('location', ''),
                        'pageSize' => (int) $request->input('pageSize'),
                    ],
                    'glints' => [
                        'SearchTerm'   => (string) $request->input('keyword'),
                        'LocationIds'  => (array) $this->account[$provider]->getConfig('location_ids', []),
                        'pageSize'     => (int) $request->input('pageSize'),
                    ]
                };

                // 6. Cari jobs
                $jobs = $this->adapter[$provider]->job()->search($params);
                $data = match ($provider) {
                    'jobstreet' => $jobs['data']['data'] ?? [],
                    'glints'    => $jobs['data']['searchJobsV3']['jobsInPage'] ?? [],
                };

                Log::info("Found " . count($data) . " jobs on $provider for user " . $this->user->id);

                // 7. Dispatch tiap job ke queue
                foreach ($data as $job) {
                    $queue = new ProcessApplications(
                        $this->adapter[$provider],
                        $this->account[$provider],
                        $job['id']
                    );
                    $queueId = Queue::connection('database')->push($queue);
                    DB::table('jobs')
                        ->where('id', $queueId)
                        ->update(['user_id' => $queue->user_id]);
                }
            }

            return redirect()->back()->with('success', 'Berhasil memasukkan ke antrian.');
        } catch (AccountNotFound $e) {
            return response()->json(['status' => 'failed', 'errors' => ['account' => [$e->getMessage()]]], 404);
        } catch (UnknownProvider $e) {
            return response()->json(['status' => 'failed', 'errors' => ['provider' => [$e->getMessage()]]], 400);
        } catch (\Exception $e) {
            Log::error('apply@push error: ' . $e->getMessage());
            return response()->json(['status' => 'failed', 'errors' => ['general' => [$e->getMessage()]]], 500);
        }
    }

    /**
     * Stop automation for current user (semua provider)
     */
    public function stop(Request $request)
    {
        Log::info('apply@stop DIPANGGIL');

        try {
            $this->user = auth()->user();

            // ✅ Set flag pause = true di USER (bukan per account)
            $this->user->automation_paused = true;
            $this->user->save();

            // Hapus semua job pending untuk user ini
            DB::table('jobs')
                ->where('payload', 'like', '%"user_id";i:' . $this->user->id . '%')
                ->delete();

            // Restart worker
            Artisan::call('queue:restart');

            return response()->json([
                'status'  => 'success',
                'message' => 'Automation stopped for all providers.'
            ]);
        } catch (\Exception $e) {
            Log::error('apply@stop error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'failed',
                'errors'  => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Resume (lanjutkan) automation for current user
     */
    public function resume(Request $request)
    {
        Log::info('apply@resume DIPANGGIL');

        try {
            $this->user = auth()->user();

            // ✅ Set flag pause = false di USER
            $this->user->automation_paused = false;
            $this->user->save();

            // Restart worker agar siap menerima job baru
            Artisan::call('queue:restart');

            return response()->json([
                'status'  => 'success',
                'message' => 'Automation resumed for all providers.'
            ]);
        } catch (\Exception $e) {
            Log::error('apply@resume error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'failed',
                'errors'  => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }
}
