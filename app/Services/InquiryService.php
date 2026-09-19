<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\Kavling;
use App\Models\SiteSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class InquiryService
{
    /**
     * Create a new inquiry.
     *
     * PRD §7.3: form publik minimal nama + phone; kavling/proyek opsional.
     * Inquiry tidak mengubah status kavling.
     *
     * @param  string  $nama
     * @param  string  $phone
     * @param  int|null  $kavlingId
     * @param  int|null  $projectId
     * @return Inquiry
     */
    public function createInquiry($nama, $phone, $kavlingId = null, $projectId = null)
    {
        $kavling = $kavlingId ? Kavling::find($kavlingId) : null;
        $projectId = $kavling?->project_id ?? $projectId;

        return DB::transaction(function () use ($nama, $phone, $kavlingId, $projectId) {
            for ($attempt = 0; $attempt < 5; $attempt++) {
                try {
                    return Inquiry::create([
                        'nama' => $nama,
                        'phone' => $phone,
                        'kavling_id' => $kavlingId,
                        'project_id' => $projectId,
                        'ref_code' => $this->nextRefCode(),
                        'status' => 'baru',
                    ]);
                } catch (QueryException $e) {
                    // Ref code duplikat akibat request bersamaan: coba lagi.
                    if (! $this->isDuplicateEntry($e)) {
                        throw $e;
                    }
                }
            }

            throw new \RuntimeException('Gagal membuat kode referensi unik.');
        });
    }

    /**
     * Bangun URL redirect WhatsApp ke nomor admin.
     *
     * PRD §5.1.5: pesan terisi nama, nomor, kavling, proyek, luas, harga, ref code.
     */
    public static function whatsappRedirect(Inquiry $inquiry): string
    {
        $inquiry->loadMissing(['kavling', 'project']);

        $admin = preg_replace('/\D/', '', (string) SiteSetting::get('whatsapp_number', '6281234567890'));

        $lines = [
            'Halo Lombok Horizon,',
            '',
            "Saya {$inquiry->nama} ({$inquiry->phone}) ingin menanyakan informasi.",
            "Ref: {$inquiry->ref_code}",
        ];

        if ($inquiry->kavling) {
            $kavling = $inquiry->kavling;
            $detail = "Kavling: {$kavling->nomor}";
            if ($kavling->luas_m2) {
                $detail .= " ({$kavling->luas_m2} m²)";
            }
            if ($kavling->harga) {
                $detail .= ' - Rp '.number_format($kavling->harga, 0, ',', '.');
            }
            $lines[] = $detail;
        }

        if ($inquiry->project) {
            $lines[] = "Proyek: {$inquiry->project->nama}";
        }

        return 'https://wa.me/'.$admin.'?text='.rawurlencode(implode("\n", $lines));
    }

    /**
     * Generate ref code format LH-{tahun}-{seq 4 digit}, mis. LH-2026-0148.
     * Satu sumber: App\Models\Inquiry::nextRefCode().
     */
    protected function nextRefCode(): string
    {
        return Inquiry::nextRefCode();
    }

    protected function isDuplicateEntry(QueryException $e): bool
    {
        return $e->getCode() === '23000';
    }
}
