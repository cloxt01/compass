<?php

namespace App\Http\Livewire;

use App\Services\Adapters\JobstreetAdapter;
use App\Clients\JobstreetAPI;
use App\Models\UserStat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public $jobs = [];
    public $userStat = null;

    public function mount()
    {
        $this->user = Auth::user();
        $this->userStat = UserStat::where('user_id', $this->user->id)->first();
        $client = new JobstreetAPI($this->user->jobstreetAccount->access_token ?? null);
        $adapter = new JobstreetAdapter($client);
        $this->jobs = $adapter->job()->applied(9999);
    }

    public function render()
    {
        $weeklyAverage = $this->hitungWeeklyAverage();
        
        return view('livewire.user-stats', [
            'jobs' => $this->jobs,
            'weeklyAverage' => $weeklyAverage,
            'userStat' => $this->userStat
        ]);
    }

    private function hitungWeeklyAverage()
    {
        $weeklyStats = $this->userStat ? $this->userStat->total_applied : 0;
        return $weeklyStats > 0 ? round($weeklyStats / 7, 1) : 0;
    }
};
?>
