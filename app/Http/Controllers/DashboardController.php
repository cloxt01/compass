<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Services\Adapters\JobstreetAdapter;
use App\Clients\JobstreetAPI;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    public function index()
    {
        $userId = auth()->user()->id;

        $appliedJobs = Application::where('user_id', $userId)->latest()->limit(5)->get();
        // 1. Data Funnel untuk Card
        $stats = Application::where('user_id', $userId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // 2. Data Chart per hari (7 hari terakhir)
        $chartData = Application::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                'status',
                DB::raw('count(*) as total')
            )
            ->groupBy('date', 'status')
            ->get()
            ->groupBy('date');
        Log::info($appliedJobs);

        return view('dashboard', [
            'appliedJobs' => $appliedJobs,
            'stats'       => $stats,
            'chartData'   => $chartData
        ]);
    }


}
