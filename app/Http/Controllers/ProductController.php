<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function compass_link(Request $request)
    {
        return view('products.compass-link');
    }
}
