<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KavlingResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\GeoJSONService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    protected $geoJSONService;

    public function __construct(GeoJSONService $geoJSONService)
    {
        $this->geoJSONService = $geoJSONService;
    }

    /**
     * Display a listing of projects.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = $this->withKavlingCounts();

        // Filter by project ID if provided (for AJAX filtering)
        if ($request->has('project_id')) {
            $query->where('id', $request->project_id);
        }

        $perPage = min((int) $request->get('per_page', 12), 50);
        $projects = $query->latest()->paginate($perPage);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'data' => ProjectResource::collection($projects),
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
            ]);
        }

        return ProjectResource::collection($projects);
    }

    /**
     * Display the specified project.
     *
     * @param  string  $slug
     * @return Response
     */
    public function show($slug)
    {
        $project = $this->withKavlingCounts()->where('slug', $slug)->firstOrFail();

        return new ProjectResource($project);
    }

    /**
     * Query project dengan hitungan kavling (total + tersedia).
     */
    protected function withKavlingCounts()
    {
        return Project::withCount([
            'kavlings',
            'kavlings as kavling_available' => function ($query) {
                $query->where('status', 'available');
            },
        ]);
    }

    /**
     * Get kavlings for a project.
     *
     * @param  string  $slug
     * @return Response
     */
    public function kavlings($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $kavlings = $project->kavlings()->get();

        return KavlingResource::collection($kavlings);
    }

    /**
     * Get GeoJSON for a project.
     *
     * @param  string  $slug
     * @return Response
     */
    public function geojson($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $geojson = $this->geoJSONService->toGeoJsonCollection($project);

        return response()->json($geojson, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
