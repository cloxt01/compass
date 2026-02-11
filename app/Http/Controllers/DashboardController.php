<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Adapters\JobstreetAdapter;
use App\Clients\JobstreetAPI;

class DashboardController extends Controller
{
    
    public function index()
    {
        $user = auth()->user();
        $appliedJobs = [];

        if ($user->jobstreetAccount) {
            $adapter = new JobstreetAdapter(new JobstreetAPI($user->jobstreetAccount->access_token));
            $appliedJobs = $adapter->job()->applied(5);
        }

        return view('dashboard.index', compact('appliedJobs'));
    }


}