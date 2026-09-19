<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Models\Booking;
use App\Services\BookingService;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kavling.nomor')
                    ->label('Kavling')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('buyer_name')
                    ->label('Nama Pembeli')
                    ->searchable(),

                TextColumn::make('buyer_phone')
                    ->label('No. HP')
                    ->searchable(),

                TextColumn::make('deal_price')
                    ->label('Harga Deal')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'verified' => 'Diverifikasi',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('booked_at')
                    ->label('Tanggal Booking')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'verified' => 'Diverifikasi',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->default('pending'),

                SelectFilter::make('kavling.project_id')
                    ->label('Proyek')
                    ->relationship('kavling.project', 'nama')
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->form([
                        Checkbox::make('update_kavling_status')
                            ->label('Ubah status kavling menjadi Dibooking')
                            ->default(false),
                    ])
                    ->action(function (array $data, Booking $booking) {
                        app(BookingService::class)
                            ->verifyBooking($booking, (bool) ($data['update_kavling_status'] ?? false));
                    })
                    ->requiresConfirmation(),

                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->form([
                        Checkbox::make('update_kavling_status')
                            ->label('Ubah status kavling menjadi Tersedia')
                            ->default(false),
                    ])
                    ->action(function (array $data, Booking $booking) {
                        app(BookingService::class)
                            ->cancelBooking($booking, (bool) ($data['update_kavling_status'] ?? false));
                    })
                    ->requiresConfirmation(),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
