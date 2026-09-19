<?php

use App\Models\Booking;
use App\Models\Kavling;
use App\Models\Project;
use App\Models\User;
use App\Services\BookingService;

function makeBooking(array $overrides = []): Booking
{
    $user = User::factory()->create();
    $project = Project::create([
        'nama' => 'Sky Lancing', 'slug' => 'sky-lancing-'.uniqid(),
        'lokasi' => 'Lombok', 'deskripsi' => 'Test',
    ]);
    $kavling = Kavling::create([
        'project_id' => $project->id, 'nomor' => 'A-001', 'status' => 'available',
    ]);

    return Booking::create(array_merge([
        'kavling_id' => $kavling->id,
        'user_id' => $user->id,
        'buyer_name' => 'Budi Santoso',
        'buyer_phone' => '081234567890',
        'deal_price' => 500000000,
        'status' => 'pending',
        'booked_at' => now(),
    ], $overrides));
}

it('verifies a booking without touching kavling status by default', function () {
    $booking = makeBooking();

    app(BookingService::class)->verifyBooking($booking);

    expect($booking->fresh()->status)->toBe('verified')
        ->and($booking->fresh()->verified_at)->not->toBeNull()
        ->and($booking->kavling->fresh()->status)->toBe('available');
});

it('verifies a booking and marks kavling booked when requested', function () {
    $booking = makeBooking();

    app(BookingService::class)->verifyBooking($booking, true);

    expect($booking->fresh()->status)->toBe('verified')
        ->and($booking->kavling->fresh()->status)->toBe('booked');
});

it('cancels a booking and restores kavling when requested', function () {
    $booking = makeBooking(['status' => 'verified']);
    $booking->kavling->update(['status' => 'booked']);

    app(BookingService::class)->cancelBooking($booking, true);

    expect($booking->fresh()->status)->toBe('cancelled')
        ->and($booking->kavling->fresh()->status)->toBe('available');
});
