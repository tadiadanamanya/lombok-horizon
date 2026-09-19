<?php

namespace App\Filament\Resources\Kavlings\Schemas;

use App\Filament\Forms\Components\GeoJSONInput;
use App\Filament\Forms\Components\MapPreviewField;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KavlingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->label('Proyek')
                    ->relationship('project', 'nama')
                    ->preload()
                    ->searchable()
                    ->required(),

                TextInput::make('nomor')
                    ->label('Nomor Kavling')
                    ->required()
                    ->maxLength(255),

                TextInput::make('luas_m2')
                    ->label('Luas (m²)')
                    ->numeric()
                    ->suffix('m²')
                    ->nullable(),

                TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->nullable(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'booked' => 'Dibooking',
                        'sold' => 'Terjual',
                        'disabled' => 'Nonaktif',
                    ])
                    ->default('available')
                    ->required(),

                GeoJSONInput::make('koordinat_bidang')
                    ->label('Koordinat Bidang (GeoJSON)')
                    ->columnSpanFull(),

                MapPreviewField::for('data.koordinat_bidang')
                    ->columnSpanFull(),
            ]);
    }
}
