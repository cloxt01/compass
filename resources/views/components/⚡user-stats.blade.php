<?php

namespace App\Http\Livewire;

use App\Services\Adapters\JobstreetAdapter;
use App\Clients\JobstreetAPI;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public $jobs = [];
    public $userStat = null;

    public function mount()
    {
        $user = Auth::user();
        $client = new JobstreetAPI($user->jobstreetAccount->access_token);

        if ($user && $user->jobstreetAccount) {
            $adapter = new JobstreetAdapter($client);
            $this->jobs = $adapter->job()->applied(9999);
            $this->userStat = $user->stats()->where('date', now()->toDateString())->first();
        }
    }

    public function render()
    {
        return view('livewire.user-stats'); // ini harus sesuai lokasi file view
    }
};
?>
