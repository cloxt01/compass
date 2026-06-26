<?php

namespace App\Http\Controllers;

use App\Services\Adapters\GlintsAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\JobstreetAccount;

use App\Clients\JobstreetAPI;
use App\Clients\GlintsAPI;
use App\Services\Adapters\JobstreetAdapter;

use App\Jobs\ProcessApplications;

use App\Exceptions\UnknownProvider;
use App\Exception\AccountNotFound;
use Illuminate\Support\Facades\Queue;


class ApplyController extends Controller {
    protected User $user;
    protected array $client;
    protected array $service;
    protected array $account;

    public function __construct() {
        $this->client = [];
        $this->service = [];
        $this->account = [];
    }
    public function index (){
        $user = auth()->user();
        $adapters = [];
        $accounts = [];

        if ($user->jobstreetAccount && $user->jobstreetAccount->access_token) {
            $accounts['jobstreet'] = $user->jobstreetAccount;
            $adapters['jobstreet'] = new JobstreetAdapter(new JobstreetAPI($user->jobstreetAccount->access_token));
        }
        if ($user->glintsAccount && $user->glintsAccount->access_token) {
            $accounts['glints'] = $user->glintsAccount;
            $adapters['glints'] = new GlintsAdapter(new GlintsAPI($user->glintsAccount->access_token, $user->glintsAccount->cookie));
        }

        return view('apply', compact('user', 'adapters', 'accounts'));
    }
    public function start(Request $request) {
        try {
            Log::info('ApplyController@start DIPANGGIL');
            $request->validate([
                'providers' => 'required|array|min:1',
                'providers.*' => 'in:jobstreet,glints',
                'keyword' => 'required',
                'pageSize' => 'required|integer|min:1|max:40',
//                'interval' => 'required|integer|min:1|max:60',
//                'max_applications' => 'required|integer|min:1|max:1000',
            ]);
            $this->user = auth()->user();

            foreach($request->input('providers') as $provider){
                // Cek dan inisialisasi akun serta klien untuk setiap provider
                $this->account[$provider] = match($provider){
                    'jobstreet' => $this->user->jobstreetAccount,
                    'glints' => $this->user->glintsAccount,
                    default => throw new UnknownProvider($provider)
                };
                // Validasi keberadaan akun untuk provider
                if(!$this->account[$provider]){
                    throw new AccountNotFound("$provider account not found");
                }

                // Inisialisasi klien
                $this->client[$provider] = match($provider){
                    'jobstreet' => new JobstreetAPI($this->user->jobstreetAccount?->access_token),
                    'glints' => new GlintsAPI($this->user->glintsAccount->access_token, $this->user->glintsAccount->cookie),
                };

                // Inisialisasi adapter
                $this->adapter[$provider] = match($provider){
                    'jobstreet' => new JobstreetAdapter($this->client[$provider]),
                    'glints' => new GlintsAdapter($this->client[$provider]),
                };

                // Cek status koneksi akun
                if($this->account[$provider]->status == 'reauth_required'){
                    return redirect()
                        ->route("platform.disconnect", ['provider' => $provider])
                        ->withErrors(['msg' => "Koneksi ke $provider terputus, silakan hubungkan ulang."]);
                }
                $params = match($provider){
                    'jobstreet' => [
                        'keyword' => $request->input('keyword'),
                        'location' => (string) ($this->account[$provider]->getConfig('location', '')),
                        'pageSize' => (int) ($request->input('pageSize'))
                    ],
                    'glints' => [
                        'SearchTerm' => (string) $request->input('keyword'),
                        'LocationIds' => (array) ($this->account[$provider]->getConfig('location_ids', [])),
                        'pageSize' => (int) ($request->input('pageSize'))

                    ]
                };
                $jobs = $this->adapter[$provider]->job()->search($params);

                $data = match($provider){
                    'jobstreet' => $jobs['data']['data'] ?? [],
                    'glints' => $jobs['data']['searchJobsV3']['jobsInPage'] ?? []
                };
                Log::info($data);
                Log::info(count($data));

                Log::info("Found " . count($data) . " jobs on $provider for user " . $this->user->id. " Params : ". json_encode($params));
                foreach($data as $job){
                    $queue = new ProcessApplications(
                        $this->adapter[$provider],
                        $this->account[$provider],
                        $job['id']
                    );
                    $queueId = Queue::connection('database')->push($queue);
                    DB::table('jobs')
                        ->where('id', $queueId)
                        ->update([
                            'user_id' => $queue->user_id
                        ]);
                }
            }


            return response()->json(['status' => 'success'], 200);
        } catch(AccountNotFound $e){
            return response()->json(['status' => 'failed', 'errors' => ['account' => [$e->getMessage()]]], 404);
        } catch (UnknownProvider $e){
            return response()->json(['status' => 'failed', 'errors' => ['provider' => [$e->getMessage()]]], 400);
        }
    }

}
