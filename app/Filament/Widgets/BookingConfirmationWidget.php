<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\Widget;

class BookingConfirmationWidget extends Widget
{
    protected static ?string $heading = 'Booking Confirmation';

    protected string $view = 'filament.widgets.booking-confirmation';

    protected function getViewData(): array
    {
        return $this->getData();
    }

    protected function getData(): array
    {
        $bookings = Booking::where('status', 'pending')
            ->with(['kavling.project', 'user'])
            ->latest('booked_at')
            ->take(5)
            ->get();

        return [
            'bookings' => $bookings,
        ];
    }
}
