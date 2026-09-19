<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    /**
     * Show the about page.
     */
    public function index()
    {
        return view('public.about');
    }
}
