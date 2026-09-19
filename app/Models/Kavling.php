<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kavling extends Model
{
    protected $fillable = [
        'project_id',
        'nomor',
        'luas_m2',
        'harga',
        'status',
        'koordinat_bidang',
    ];

    protected $casts = [
        'koordinat_bidang' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
