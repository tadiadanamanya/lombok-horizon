<?php

namespace App\Services;

use App\Models\Booking;

class BookingService
{
    /**
     * Verifikasi booking. Status kavling hanya berubah bila diminta admin.
     */
    public function verifyBooking(Booking $booking, bool $updateKavlingStatus = false): Booking
    {
        $booking->status = 'verified';
        $booking->verified_at = now();
        $booking->save();

        if ($updateKavlingStatus && $booking->kavling) {
            $booking->kavling->status = 'booked';
            $booking->kavling->save();
        }

        return $booking;
    }

    /**
     * Batalkan booking. Status kavling hanya berubah bila diminta admin.
     */
    public function cancelBooking(Booking $booking, bool $updateKavlingStatus = false): Booking
    {
        $booking->status = 'cancelled';
        $booking->save();

        if ($updateKavlingStatus && $booking->kavling) {
            $booking->kavling->status = 'available';
            $booking->kavling->save();
        }

        return $booking;
    }
}
