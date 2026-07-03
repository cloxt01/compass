<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController
{



    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $request->input('per_page', 15);
        $user = auth()->user();

        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $query = Application::where('user_id', $user->id);

        $stats = [
            'total' => $query->count(),
            'success' => (clone $query)->where('status', 'success')->count(),
            'applied' => (clone $query)->where('status', 'applied')->count(),
            'questionnaire' => (clone $query)->where('status', 'questionnaire')->count(),
        ];

        $applications = $query->latest()->paginate($perPage);

        $applications->appends(['per_page' => $perPage]);

        return view('applications', compact('user', 'applications', 'stats', 'perPage'));
    }
}
