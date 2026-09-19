<?php

use App\Models\Kavling;
use App\Models\Project;
use App\Services\GeoJSONService;

function makePolygon(): array
{
    // Proper GeoJSON: [lng, lat], ring tertutup.
    $ring = [[116.28, -8.73], [116.29, -8.73], [116.29, -8.72], [116.28, -8.72], [116.28, -8.73]];

    return ['type' => 'Polygon', 'coordinates' => [$ring]];
}

it('converts a kavling to a valid GeoJSON feature', function () {
    $kavling = new Kavling([
        'nomor' => 'A-001', 'luas_m2' => 500, 'harga' => 350000000,
        'status' => 'available', 'project_id' => 1,
        'koordinat_bidang' => makePolygon(),
    ]);

    $feature = app(GeoJSONService::class)->toGeoJsonFeature($kavling);

    expect($feature['type'])->toBe('Feature')
        ->and($feature['geometry']['type'])->toBe('Polygon')
        ->and($feature['properties']['nomor'])->toBe('A-001')
        ->and($feature['properties']['status'])->toBe('available');
});

it('keeps longitude-latitude order with a closed ring', function () {
    $kavling = new Kavling(['koordinat_bidang' => makePolygon()]);

    $ring = app(GeoJSONService::class)->toGeoJsonFeature($kavling)['geometry']['coordinates'][0];

    expect($ring[0])->toBe([116.28, -8.73])
        ->and(end($ring))->toBe($ring[0]);
});

it('converts a project to a feature collection', function () {
    $project = Project::create([
        'nama' => 'Sky Lancing', 'slug' => 'sky-lancing',
        'lokasi' => 'Lombok', 'deskripsi' => 'Test',
    ]);
    Kavling::create(['project_id' => $project->id, 'nomor' => 'A-001', 'koordinat_bidang' => makePolygon()]);
    Kavling::create(['project_id' => $project->id, 'nomor' => 'A-002', 'koordinat_bidang' => makePolygon()]);

    $collection = app(GeoJSONService::class)->toGeoJsonCollection($project->fresh());

    expect($collection['type'])->toBe('FeatureCollection')
        ->and($collection['features'])->toHaveCount(2);
});

it('emits null geometry instead of a fake point when coordinates are empty', function () {
    $kavling = new Kavling(['nomor' => 'A-009', 'status' => 'available', 'project_id' => 1]);

    $feature = app(GeoJSONService::class)->toGeoJsonFeature($kavling);

    expect($feature['type'])->toBe('Feature')
        ->and($feature['geometry'])->toBeNull()
        ->and($feature['properties']['kind'])->toBe('kavling');
});

it('prepends the project boundary as the first collection feature', function () {
    $project = Project::create([
        'nama' => 'Sky Lancing', 'slug' => 'sky-lancing-boundary',
        'lokasi' => 'Lombok', 'deskripsi' => 'Test',
        'batas_proyek' => makePolygon(),
    ]);
    Kavling::create(['project_id' => $project->id, 'nomor' => 'A-001', 'koordinat_bidang' => makePolygon()]);

    $collection = app(GeoJSONService::class)->toGeoJsonCollection($project->fresh());

    expect($collection['features'])->toHaveCount(2)
        ->and($collection['features'][0]['properties'])->toMatchArray(['kind' => 'boundary', 'project' => 'Sky Lancing'])
        ->and($collection['features'][0]['geometry']['type'])->toBe('Polygon')
        ->and($collection['features'][1]['properties']['kind'])->toBe('kavling');
});
