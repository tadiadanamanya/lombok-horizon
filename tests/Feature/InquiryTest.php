<?php

use App\Models\Inquiry;
use App\Models\Kavling;
use App\Models\Project;
use App\Models\SiteSetting;

function makeKavling(): Kavling
{
    $project = Project::create([
        'nama' => 'Sky Lancing', 'slug' => 'sky-lancing-'.uniqid(),
        'lokasi' => 'Lombok', 'deskripsi' => 'Test',
    ]);

    return Kavling::create(['project_id' => $project->id, 'nomor' => 'A-001']);
}

it('stores an inquiry with ref code and whatsapp redirect', function () {
    $kavling = makeKavling();

    $response = $this->postJson('/api/v1/inquiries', [
        'nama' => 'Budi Santoso',
        'phone' => '081234567890',
        'kavling_id' => $kavling->id,
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['data' => ['ref_code', 'redirect_url']]);

    $data = $response->json('data');
    expect($data['ref_code'])->toMatch('/^LH-\d{4}-\d{3,}$/')
        ->and($data['redirect_url'])->toStartWith('https://wa.me/');

    $this->assertDatabaseHas('inquiries', ['ref_code' => $data['ref_code'], 'status' => 'baru']);
});

it('generates unique ref codes', function () {
    $kavling = makeKavling();
    $payload = ['nama' => 'Budi Santoso', 'phone' => '081234567890', 'kavling_id' => $kavling->id];

    $first = $this->postJson('/api/v1/inquiries', $payload)->json('data.ref_code');
    $second = $this->postJson('/api/v1/inquiries', $payload)->json('data.ref_code');

    expect($first)->not->toBe($second);
    expect(Inquiry::count())->toBe(2);
});

it('rejects invalid payloads', function () {
    $response = $this->postJson('/api/v1/inquiries', [
        'nama' => 'A',
        'phone' => 'bukan-nomor',
        'kavling_id' => 999999,
    ]);

    $response->assertStatus(422);
    expect(Inquiry::count())->toBe(0);
});

it('rate limits inquiry submissions', function () {
    $kavling = makeKavling();
    $payload = ['nama' => 'Budi Santoso', 'phone' => '081234567890', 'kavling_id' => $kavling->id];

    foreach (range(1, 5) as $i) {
        $this->postJson('/api/v1/inquiries', $payload)->assertCreated();
    }

    $this->postJson('/api/v1/inquiries', $payload)->assertStatus(429);
});

it('accepts an inquiry without kavling and redirects to admin whatsapp', function () {
    SiteSetting::set('whatsapp_number', '6289876543210');

    $response = $this->postJson('/api/v1/inquiries', [
        'nama' => 'Siti Aminah',
        'phone' => '081234567890',
    ]);

    $response->assertCreated();
    $data = $response->json('data');

    expect($data['ref_code'])->toMatch('/^LH-\d{4}-\d{4}$/')
        ->and($data['redirect_url'])->toStartWith('https://wa.me/6289876543210?text=')
        ->and(urldecode(explode('text=', $data['redirect_url'])[1]))
        ->toContain('Siti Aminah', $data['ref_code']);

    $this->assertDatabaseHas('inquiries', ['ref_code' => $data['ref_code'], 'kavling_id' => null]);
});

it('derives project_id from kavling', function () {
    $kavling = makeKavling();

    $response = $this->postJson('/api/v1/inquiries', [
        'nama' => 'Budi Santoso',
        'phone' => '081234567890',
        'kavling_id' => $kavling->id,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('inquiries', [
        'kavling_id' => $kavling->id,
        'project_id' => $kavling->project_id,
    ]);
});

it('auto-generates ref code on model create without one', function () {
    $inquiry = Inquiry::create([
        'nama' => 'Admin Input',
        'phone' => '081234567890',
        'status' => 'baru',
    ]);

    expect($inquiry->ref_code)->toMatch('/^LH-\d{4}-\d{4}$/');
});

it('generates sequential unique ref codes on model creates', function () {
    $first = Inquiry::create(['nama' => 'Satu', 'phone' => '081234567890', 'status' => 'baru']);
    $second = Inquiry::create(['nama' => 'Dua', 'phone' => '081234567891', 'status' => 'baru']);

    expect($first->ref_code)->not->toBe($second->ref_code)
        ->and((int) substr($second->ref_code, -4))->toBe((int) substr($first->ref_code, -4) + 1);
});
