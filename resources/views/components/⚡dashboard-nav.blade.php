<?php

namespace App\Http\Livewire;
use Livewire\Component;
use Illuminate\Support\Facades\Route;

new class extends Component
{
    public $currentRoute;

    public function mount()
    {
        $this->currentRoute = Route::currentRouteName();
    }

    public function render()
    {
        return view('livewire.dashboard-nav');
    }
}
?>
