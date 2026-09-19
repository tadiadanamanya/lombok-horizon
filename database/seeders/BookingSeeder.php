<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Kavling;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * 8 booking demo: 4 verified, 2 pending, 2 cancelled.
     * verified_at hanya diisi untuk status verified.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // [slug proyek, nomor kavling, nama, phone, deal, fee, status, hari lalu, hari verifikasi lalu]
            ['sky-lancing', 'A-003', 'Budi Santoso', '081234567801', 350000000, 70000000, 'verified', 45, 40],
            ['sky-lancing', 'B-002', 'Siti Rahayu', '081234567802', 400000000, 80000000, 'verified', 30, 25],
            ['kuta-central-flat', 'A-003', 'Agus Wijaya', '081234567803', 300000000, 60000000, 'verified', 20, 15],
            ['senggigi-bay-view', 'B-002', 'Dewi Lestari', '081234567804', 450000000, 90000000, 'verified', 10, 5],
            ['torok-hill-residence', 'A-005', 'Hendra Gunawan', '081234567805', 275000000, 55000000, 'pending', 3, null],
            ['pujut-flat', 'B-006', 'Rina Marlina', '081234567806', 250000000, 50000000, 'pending', 1, null],
            ['kuta-central-flat', 'A-005', 'Joko Prasetyo', '081234567807', 300000000, 60000000, 'cancelled', 60, null],
            ['pujut-flat', 'A-003', 'Ayu Puspita', '081234567808', 250000000, 50000000, 'cancelled', 50, null],
        ];

        foreach ($rows as [$slug, $nomor, $nama, $phone, $deal, $fee, $status, $daysAgo, $verifiedDaysAgo]) {
            $project = Project::where('slug', $slug)->firstOrFail();
            $kavling = Kavling::where('project_id', $project->id)->where('nomor', $nomor)->firstOrFail();

            Booking::updateOrCreate(
                ['kavling_id' => $kavling->id, 'buyer_phone' => $phone],
                [
                    'user_id' => 1,
                    'buyer_name' => $nama,
                    'buyer_email' => null,
                    'booking_fee' => $fee,
                    'deal_price' => $deal,
                    'status' => $status,
                    'booked_at' => $now->copy()->subDays($daysAgo),
                    'verified_at' => $verifiedDaysAgo ? $now->copy()->subDays($verifiedDaysAgo) : null,
                ]
            );
        }
    }
}
