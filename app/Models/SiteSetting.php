<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class SiteSetting extends Model
{
    use LogsActivity;

    /**
     * Hanya key yang nilainya benar berubah yang tercatat (bukan tiap save).
     */
    protected static $recordEvents = ['updated'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['value'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'key',
        'value',
        'updated_by',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ambil nilai setting berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->first()?->value ?? $default;
    }

    /**
     * Simpan nilai setting berdasarkan key.
     */
    public static function set(string $key, mixed $value, ?int $updatedBy = null): static
    {
        return tap(static::firstOrNew(['key' => $key]), function (SiteSetting $setting) use ($value, $updatedBy) {
            $setting->value = $value;
            $setting->updated_by = $updatedBy;
            $setting->save();
        });
    }
}
