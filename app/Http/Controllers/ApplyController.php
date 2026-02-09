<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\JobstreetAccount;

use App\Clients\JobstreetAPI;
use App\Clients\GlintsAPI;
use App\Services\Adapters\JobstreetAdapter;

use App\Jobs\ProcessApplications;

use App\Exceptions\UnknownProvider;
use App\Exception\AccountNotFound;


class ApplyController extends Controller {
    protected User $user;
    protected array $client;
    protected array $service;
    protected array $provider_account;
    
    public function __construct() {
        $this->client = [];
        $this->service = [];
        $this->provider_account = [];
    }
    public function start(Request $request) {
        try {
            $request->validate([
                'providers' => 'required|array|min:1',
                'providers.*' => 'in:jobstreet',
                'keyword' => 'required',
                'location' => 'required|min:3',
                'pageSize' => 'required|integer|min:1|max:50',
                'interval' => 'required|integer|min:1|max:60',
                'max_applications' => 'required|integer|min:1|max:1000',
            ]);
            $this->user = auth()->user();
            
            foreach($request->input('providers') as $provider){
                // Cek dan inisialisasi akun serta klien untuk setiap provider
                $this->provider_account[$provider] = match($provider){
                    'jobstreet' => $this->user->jobstreetAccount,
                    default => throw new UnknownProvider($provider)
                };
                // Validasi keberadaan akun untuk provider
                if(!$this->provider_account[$provider]){
                    throw new AccountNotFound("$provider account not found");
                }

                // Inisialisasi klien 
                $this->client[$provider] = match($provider){
                    'jobstreet' => new JobstreetAPI($this->user->jobstreetAccount?->access_token),
                };
                
                // Inisialisasi adapter 
                $this->adapter[$provider] = match($provider){
                    'jobstreet' => new JobstreetAdapter($this->client[$provider]),
                };
                
                // Cek status koneksi akun
                if($this->provider_account[$provider]->status == 'reauth_required'){
                    return redirect()
                        ->route("api.external.disconnect", ['provider' => $provider]) 
                        ->withErrors(['msg' => "Koneksi ke $provider terputus, silakan hubungkan ulang."]);
                }
                $jobs = $this->adapter[$provider]->job()->search([
                    'keyword' => $request->input('keyword'),
                    'location' => $request->input('location'),
                    'pageSize' => $request->input('pageSize')
                ]);
                Log::info("Found " . count($jobs['data']['data']) . " jobs on $provider for user " . $this->user->id);
                foreach($jobs['data']['data'] as $job){
                    ProcessApplications::dispatch($this->adapter[$provider], $job['id']);
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