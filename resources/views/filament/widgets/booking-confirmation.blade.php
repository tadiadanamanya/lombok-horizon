<div class="space-y-4">
    <div class="flex justify-between items-start">
        <h2 class="text-lg font-semibold">Konfirmasi Booking</h2>
        <a href="{{ url('/admin/bookings') }}" class="text-sm text-primary hover:underline">Lihat Semua</a>
    </div>

    @if ($bookings->isEmpty())
        <p class="text-center text-muted-foreground">Tidak ada booking yang menunggu konfirmasi.</p>
    @else
        <div class="space-y-3">
            @foreach ($bookings as $booking)
                <div class="border p-3 rounded-md">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium">{{ $booking->kavling ? $booking->kavling->nomor : '-' }}</p>
                            <p class="text-sm text-muted-foreground">{{ $booking->buyer_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">Rp {{ number_format($booking->deal_price, 0, ',', '.') }}</p>
                            <p class="text-sm text-muted-foreground">{{ $booking->booked_at ? $booking->booked_at->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                </div>
                @if (!$loop->last)
                    <div class="border-t border-dashed my-2"></div>
                @endif
            @endforeach
        </div>
    @endif
</div>