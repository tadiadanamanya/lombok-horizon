<?php

use App\Models\Kavling;
use App\Models\Project;

function makeProjectWithKavlings(): Project
{
    $ring = [[116.28, -8.73], [116.29, -8.73], [116.29, -8.72], [116.28, -8.72], [116.28, -8.73]];
    $polygon = ['type' => 'Polygon', 'coordinates' => [$ring]];

    $project = Project::create([
        'nama' => 'Sky Lancing', 'slug' => 'sky-lancing-'.uniqid(),
        'lokasi' => 'Lombok', 'deskripsi' => 'Test',
        'batas_proyek' => $polygon,
    ]);

    Kavling::create([
        'project_id' => $project->id, 'nomor' => 'A-001',
        'luas_m2' => 500, 'harga' => 350000000, 'status' => 'available',
        'koordinat_bidang' => $polygon,
    ]);

    return $project;
}

it('lists projects with kavling counts', function () {
    $project = makeProjectWithKavlings();

    $response = $this->getJson('/api/v1/projects');

    $response->assertOk()
        ->assertJsonStructure(['data' => [['id', 'nama', 'slug', 'kavling_total', 'kavling_available']]])
        ->assertJsonPath('data.0.slug', $project->slug)
        ->assertJsonPath('data.0.kavling_total', 1)
        ->assertJsonPath('data.0.kavling_available', 1);
});

it('shows a single project', function () {
    $project = makeProjectWithKavlings();

    $this->getJson("/api/v1/projects/{$project->slug}")
        ->assertOk()
        ->assertJsonPath('data.nama', 'Sky Lancing');
});

it('returns 404 for an unknown project slug', function () {
    $this->getJson('/api/v1/projects/tidak-ada')->assertNotFound();
    $this->getJson('/api/v1/projects/tidak-ada/kavlings')->assertNotFound();
    $this->getJson('/api/v1/projects/tidak-ada/geojson')->assertNotFound();
});

it('lists kavlings without coordinates', function () {
    $project = makeProjectWithKavlings();

    $response = $this->getJson("/api/v1/projects/{$project->slug}/kavlings");

    $response->assertOk()
        ->assertJsonStructure(['data' => [['id', 'nomor', 'luas_m2', 'harga', 'status']]])
        ->assertJsonPath('data.0.nomor', 'A-001');

    expect($response->json('data.0'))->not->toHaveKey('koordinat_bidang');
});

it('lists kavlings in deterministic nomor order', function () {
    $project = makeProjectWithKavlings();
    $polygon = ['type' => 'Polygon', 'coordinates' => [[[116.28, -8.73], [116.29, -8.73], [116.29, -8.72], [116.28, -8.72], [116.28, -8.73]]]];

    // Insert acak: C, A (A-001 sudah ada dari helper).
    foreach (['C-003', 'B-002'] as $nomor) {
        Kavling::create([
            'project_id' => $project->id, 'nomor' => $nomor,
            'luas_m2' => 100, 'harga' => 100000000, 'status' => 'available',
            'koordinat_bidang' => $polygon,
        ]);
    }

    $nomors = $this->getJson("/api/v1/projects/{$project->slug}/kavlings")
        ->assertOk()
        ->json('data.*.nomor');

    expect($nomors)->toBe(['A-001', 'B-002', 'C-003']);
});

it('shows a single kavling without coordinates', function () {
    $project = makeProjectWithKavlings();

    $this->getJson('/api/v1/kavlings/'.$project->kavlings()->first()->id)
        ->assertOk()
        ->assertJsonPath('data.nomor', 'A-001');
});

it('serves project geojson with boundary first', function () {
    $project = makeProjectWithKavlings();

    $response = $this->getJson("/api/v1/projects/{$project->slug}/geojson");

    $response->assertOk()->assertHeader('Content-Type', 'application/json');

    $geojson = $response->json();

    expect($geojson['type'])->toBe('FeatureCollection')
        ->and($geojson['features'])->toHaveCount(2)
        ->and($geojson['features'][0]['properties']['kind'])->toBe('boundary')
        ->and($geojson['features'][1]['properties'])->toMatchArray(['kind' => 'kavling', 'nomor' => 'A-001'])
        ->and($geojson['features'][1]['geometry']['coordinates'][0][0])->toBe([116.28, -8.73]);
});

it('serves kavling geojson feature', function () {
    $project = makeProjectWithKavlings();

    $response = $this->getJson('/api/v1/kavlings/'.$project->kavlings()->first()->id.'/geojson');

    $response->assertOk();

    expect($response->json('type'))->toBe('Feature')
        ->and($response->json('geometry.type'))->toBe('Polygon')
        ->and($response->json('properties.status'))->toBe('available');
});

it('returns 404 for an unknown kavling', function () {
    $this->getJson('/api/v1/kavlings/999999')->assertNotFound();
    $this->getJson('/api/v1/kavlings/999999/geojson')->assertNotFound();
});
