<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Kavling;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_status_change_is_logged(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create tidak tercatat (hanya updated).
        $booking = Booking::create([
            'kavling_id' => Kavling::create([
                'project_id' => Project::create([
                    'nama' => 'Lombok Horizon', 'slug' => 'lombok-horizon',
                    'lokasi' => 'Lombok', 'deskripsi' => 'Test',
                ])->id,
                'nomor' => 'A-001',
            ])->id,
            'user_id' => $user->id,
            'buyer_name' => 'Budi Santoso',
            'buyer_phone' => '081234567890',
            'deal_price' => 500000000,
            'status' => 'pending',
            'booked_at' => now(),
        ]);
        $this->assertEquals(0, Activity::count());

        // Update non-status tidak tercatat.
        $booking->update(['buyer_name' => 'Budi S.']);
        $this->assertEquals(0, Activity::count());

        // Verify (transisi status) tercatat dengan causer admin.
        $booking->update(['status' => 'verified']);
        $this->assertEquals(1, Activity::count());

        $activity = Activity::first();
        $this->assertEquals('updated', $activity->description);
        $this->assertTrue($activity->subject->is($booking));
        $this->assertTrue($activity->causer->is($user));
    }

    public function test_site_setting_change_is_logged(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $setting = SiteSetting::create(['key' => 'site_name', 'value' => 'Lama']);
        $this->assertEquals(0, Activity::count());

        // Save tanpa perubahan tidak tercatat.
        $setting->touch();
        $this->assertEquals(0, Activity::count());

        $setting->update(['value' => 'Baru']);
        $this->assertEquals(1, Activity::count());
        $this->assertEquals('updated', Activity::first()->description);
    }

    public function test_activity_log_page_renders(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/activity-logs')->assertOk();
    }
}
