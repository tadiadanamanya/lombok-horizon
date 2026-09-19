<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'nama',
        'phone',
        'kavling_id',
        'project_id',
        'ref_code',
        'status',
    ];

    protected static function booted(): void
    {
        // Auto-generate ref_code agar jalur non-API (admin, seeder) konsisten.
        static::creating(function (Inquiry $inquiry): void {
            if (blank($inquiry->ref_code)) {
                $inquiry->ref_code = static::nextRefCode();
            }
        });
    }

    /**
     * Generate ref code format LH-{tahun}-{seq 4 digit}, mis. LH-2026-0148.
     */
    public static function nextRefCode(): string
    {
        $year = Carbon::now()->year;

        $latest = static::where('ref_code', 'like', "LH-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->ref_code, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return sprintf('LH-%d-%04d', $year, $sequence);
    }

    public function kavling()
    {
        return $this->belongsTo(Kavling::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
