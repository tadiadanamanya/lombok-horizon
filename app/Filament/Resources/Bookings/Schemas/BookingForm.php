<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Models\Kavling;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kavling_id')
                    ->label('Kavling')
                    ->relationship('kavling', 'nomor')
                    ->preload()
                    ->searchable()
                    ->default(fn () => Kavling::where('status', 'available')->first()?->id)
                    ->nullable(),

                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),

                DateTimePicker::make('booked_at')
                    ->label('Tanggal Booking')
                    ->default(now())
                    ->required(),

                TextInput::make('buyer_name')
                    ->label('Nama Pembeli')
                    ->required()
                    ->maxLength(255),

                TextInput::make('buyer_phone')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->required(),

                TextInput::make('buyer_email')
                    ->label('Email Pembeli')
                    ->email()
                    ->nullable(),

                TextInput::make('booking_fee')
                    ->label('Booking Fee')
                    ->numeric()
                    ->prefix('Rp ')
                    ->nullable(),

                TextInput::make('deal_price')
                    ->label('Deal Price')
                    ->numeric()
                    ->prefix('Rp ')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'verified' => 'Diverifikasi',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
