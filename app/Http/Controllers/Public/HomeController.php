<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;

class HomeController extends Controller
{
    /**
     * Show the application homepage.
     */
    public function index()
    {
        // Get featured projects (latest 6)
        $featuredProjects = Project::withCount([
            'kavlings as kavling_total',
            'kavlings as kavling_available' => function ($query) {
                $query->where('status', 'available');
            },
        ])
            ->latest()
            ->limit(6)
            ->get();

        return view('public.home', compact('featuredProjects'));
    }
}
