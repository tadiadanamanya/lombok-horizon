<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KavlingResource;
use App\Models\Kavling;
use App\Services\GeoJSONService;
use Illuminate\Http\Response;

class KavlingController extends Controller
{
    protected $geoJSONService;

    public function __construct(GeoJSONService $geoJSONService)
    {
        $this->geoJSONService = $geoJSONService;
    }

    /**
     * Display the specified kavling.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $kavling = Kavling::findOrFail($id);

        return new KavlingResource($kavling);
    }

    /**
     * Get GeoJSON for a kavling.
     *
     * @param  int  $id
     * @return Response
     */
    public function geojson($id)
    {
        $kavling = Kavling::findOrFail($id);
        $geojson = $this->geoJSONService->toGeoJsonFeature($kavling);

        return response()->json($geojson, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
