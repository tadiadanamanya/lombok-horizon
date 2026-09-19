<?php

use App\Models\Kavling;
use App\Models\Project;
use App\Services\GeoJSONService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeoJSONDrawingTest extends TestCase
{
    use RefreshDatabase;

    public function test_geojson_service_handles_geometry_object()
    {
        $geoJSONService = app(GeoJSONService::class);

        // Create a mock kavling with geometry object coordinates
        $kavling = new Kavling([
            'koordinat_bidang' => [
                'type' => 'Polygon',
                'coordinates' => [[
                    [116.0, -8.5],
                    [116.1, -8.5],
                    [116.1, -8.6],
                    [116.0, -8.6],
                    [116.0, -8.5],
                ]],
            ],
        ]);

        $feature = $geoJSONService->toGeoJsonFeature($kavling);

        $this->assertEquals('Feature', $feature['type']);
        $this->assertEquals('Polygon', $feature['geometry']['type']);
        $this->assertEquals(
            [[[116.0, -8.5], [116.1, -8.5], [116.1, -8.6], [116.0, -8.6], [116.0, -8.5]]],
            $feature['geometry']['coordinates']
        );
        $this->assertEquals('kavling', $feature['properties']['kind']);
    }

    public function test_geojson_service_handles_raw_coordinates_as_linestring()
    {
        // Test that GeoJSONService properly handles raw coordinates array as LineString
        $geoJSONService = app(GeoJSONService::class);

        // Create a mock kavling with raw coordinates (will be interpreted as LineString)
        $kavling = new Kavling([
            'koordinat_bidang' => [
                [116.0, -8.5],
                [116.1, -8.5],
                [116.1, -8.6],
                [116.0, -8.6],
                [116.0, -8.5],
            ],
        ]);

        $feature = $geoJSONService->toGeoJsonFeature($kavling);

        $this->assertEquals('Feature', $feature['type']);
        $this->assertEquals('LineString', $feature['geometry']['type']);
        $this->assertEquals(
            [[116.0, -8.5], [116.1, -8.5], [116.1, -8.6], [116.0, -8.6], [116.0, -8.5]],
            $feature['geometry']['coordinates']
        );
    }

    public function test_project_geojson_endpoint_returns_featurecollection()
    {
        // Create project with boundary
        $project = new Project([
            'nama' => 'Test Project',
            'slug' => 'test-project',
            'lokasi' => 'Test Location',
            'deskripsi' => 'Test description',
            'batas_proyek' => [
                'type' => 'Polygon',
                'coordinates' => [[
                    [115.9, -8.4],
                    [116.2, -8.4],
                    [116.2, -8.7],
                    [115.9, -8.7],
                    [115.9, -8.4],
                ]],
            ],
        ]);
        $project->save();

        // Add a kavling to the project
        $kavling = new Kavling([
            'project_id' => $project->id,
            'nomor' => 'A-001',
            'luas_m2' => 500,
            'harga' => 350000000,
            'status' => 'available',
            'koordinat_bidang' => [
                'type' => 'Polygon',
                'coordinates' => [[
                    [116.0, -8.5],
                    [116.1, -8.5],
                    [116.1, -8.6],
                    [116.0, -8.6],
                    [116.0, -8.5],
                ]],
            ],
        ]);
        $kavling->save();

        $response = $this->getJson("/api/v1/projects/{$project->slug}/geojson");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $geojson = json_decode($response->getContent(), true);

        $this->assertEquals('FeatureCollection', $geojson['type']);
        $this->assertEquals(2, count($geojson['features']));

        $this->assertEquals('boundary', $geojson['features'][0]['properties']['kind'] ?? null);
        $this->assertEquals('Polygon', $geojson['features'][0]['geometry']['type']);

        $this->assertEquals('kavling', $geojson['features'][1]['properties']['kind'] ?? null);
        $this->assertEquals('Polygon', $geojson['features'][1]['geometry']['type']);
    }
}
