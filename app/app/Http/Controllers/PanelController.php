<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class PanelController extends Controller {

    public function start(Request $request) {

        $request->validate([
            'keyword' => 'required',
            'location' => 'required:min:3',
            'page_size' => 'required|integer|min:1|max:50',
            'interval' => 'required|integer|min:1|max:60',
            'max_applications' => 'required|integer|min:1|max:1000',
        ]);

        Log::info('panel.start success');
        return response()->json(['status' => 'success'], 200);
    }
}