<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\Kavling;
use App\Models\Project;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    /**
     * 8 inquiry demo, mix 4 status.
     * ref_code diisi eksplisit karena DatabaseSeeder memakai
     * WithoutModelEvents (hook auto-generate tidak jalan saat seed).
     */
    public function run(): void
    {
        $sky = Project::where('slug', 'sky-lancing')->firstOrFail();
        $kuta = Project::where('slug', 'kuta-central-flat')->firstOrFail();
        $kavA1 = Kavling::where('project_id', $sky->id)->where('nomor', 'A-001')->firstOrFail();
        $kavB1 = Kavling::where('project_id', $kuta->id)->where('nomor', 'B-001')->firstOrFail();

        $rows = [
            // [nama, phone, kavling_id, project_id, ref, status]
            ['Andi Pratama', '082134567801', $kavA1->id, $sky->id, 'LH-2026-0001', 'baru'],
            ['Fitri Handayani', '082134567802', null, $sky->id, 'LH-2026-0002', 'baru'],
            ['Rudi Hartono', '082134567803', $kavB1->id, $kuta->id, 'LH-2026-0003', 'dihubungi'],
            ['Nina Kurnia', '082134567804', null, $kuta->id, 'LH-2026-0004', 'dihubungi'],
            ['Dedi Supriadi', '082134567805', $kavA1->id, $sky->id, 'LH-2026-0005', 'deal'],
            ['Wulan Sari', '082134567806', null, null, 'LH-2026-0006', 'deal'],
            ['Eko Saputra', '082134567807', null, $sky->id, 'LH-2026-0007', 'ditolak'],
            ['Yuni Astuti', '082134567808', null, null, 'LH-2026-0008', 'ditolak'],
        ];

        foreach ($rows as [$nama, $phone, $kavlingId, $projectId, $ref, $status]) {
            Inquiry::updateOrCreate(
                ['ref_code' => $ref],
                [
                    'nama' => $nama,
                    'phone' => $phone,
                    'kavling_id' => $kavlingId,
                    'project_id' => $projectId,
                    'status' => $status,
                ]
            );
        }
    }
}
