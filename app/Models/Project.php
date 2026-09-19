<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'nama',
        'slug',
        'lokasi',
        'deskripsi',
        'thumbnail_path',
        'batas_proyek',
    ];

    protected $casts = [
        'batas_proyek' => 'array',
    ];

    public function kavlings()
    {
        return $this->hasMany(Kavling::class)->orderBy('nomor');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }
}
