<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exceptions\UnknownProvider;
use App\Exception\AccountNotFound;



class PanelController extends Controller {

    public function start(Request $request) {
        try {
            $request->validate([
                'providers' => 'required|array|min:1',
                'providers.*' => 'in:jobstreet',
                'keyword' => 'required',
                'location' => 'required:min:3',
                'page_size' => 'required|integer|min:1|max:50',
                'interval' => 'required|integer|min:1|max:60',
                'max_applications' => 'required|integer|min:1|max:1000',
            ]);
            $provider_account = [];
            foreach($request->input('providers') as $provider){
                $provider_account[$provider] = match($provider){
                    'jobstreet' => auth()->user()->jobstreetAccount,
                    default => throw new UnknownProvider($provider)
                };
            }
            foreach($provider_account as $provider => $account){
                if(!$account){
                    throw new AccountNotFound("$provider account not found");
                }
                if($account->status == 'reauth_required'){
                    return redirect()
                        ->route("api.external.disconnect", ['provider' => $provider]) 
                        ->withErrors(['msg' => "Koneksi ke $provider terputus, silakan hubungkan ulang."]);
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