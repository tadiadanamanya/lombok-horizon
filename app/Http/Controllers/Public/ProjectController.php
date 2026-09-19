<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Show a list of all projects.
     */
    public function index(Request $request)
    {
        $query = Project::withCount([
            'kavlings as kavling_total',
            'kavlings as kavling_available' => function ($query) {
                $query->where('status', 'available');
            },
        ]);

        // Filter by project ID if provided (for AJAX filtering)
        if ($request->has('project_id')) {
            $query->where('id', $request->project_id);
        }

        $projects = $query->latest()->paginate(12);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'data' => ProjectResource::collection($projects),
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
            ]);
        }

        return view('public.projects.index', compact('projects'));
    }

    /**
     * Show a single project.
     */
    public function show(Project $project)
    {
        // Load project with images and kavlings count (tanpa koordinat: GeoJSON via endpoint terpisah)
        $project->load(['images', 'kavlings' => function ($query) {
            $query->select('id', 'project_id', 'nomor', 'luas_m2', 'harga', 'status');
        }]);

        // Calculate kavlings stats
        $kavlings = $project->kavlings;
        $kavlingsTotal = $kavlings->count();
        $kavlingsAvailable = $kavlings->where('status', 'available')->count();
        $kavlingsSold = $kavlings->where('status', 'sold')->count();

        // Add stats to project for easy access in view
        $project->kavling_total = $kavlingsTotal;
        $project->kavling_available = $kavlingsAvailable;
        $project->kavling_sold = $kavlingsSold;

        return view('public.projects.show', compact('project'));
    }
}
