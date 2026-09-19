<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Booking extends Model
{
    use LogsActivity;

    /**
     * Hanya transisi status (verify/cancel) yang tercatat; create/delete/edit lain tidak.
     */
    protected static $recordEvents = ['updated'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'kavling_id',
        'user_id',
        'buyer_name',
        'buyer_phone',
        'buyer_email',
        'booking_fee',
        'deal_price',
        'status',
        'booked_at',
        'verified_at',
    ];

    protected $casts = [
        'booked_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function kavling()
    {
        return $this->belongsTo(Kavling::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
