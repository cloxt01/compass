<?php

namespace App\Http\Livewire;

use App\Services\Adapters\JobstreetAdapter;
use App\Clients\JobstreetAPI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

new class extends Component
{
    public $jobs = [];

    public function mount()
    {
        $user = Auth::user();

        if ($user && $user->jobstreetAccount) {
            $adapter = new JobstreetAdapter(new JobstreetAPI($user->jobstreetAccount->access_token));
            Log::info("Loading applied jobs for user {".json_encode($adapter->loadProfile())."} using JobstreetAdapter.");
            $this->jobs = $adapter->job()->applied(9999);
            Log::info("Applied jobs loaded for user {$user->id}: " . count($this->jobs) . " jobs found.");
        }
    }

    public function render()
    {
        return view('livewire.applied-jobs'); // ini harus sesuai lokasi file view
    }
};
?>
