<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Kavlings\Schemas\KavlingForm;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KavlingsRelationManager extends RelationManager
{
    protected static string $relationship = 'kavlings';

    protected static ?string $title = 'Kavling';

    public function form(Schema $schema): Schema
    {
        return KavlingForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('luas_m2')
                    ->label('Luas (m\u00b2)')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'booked' => 'Dibooking',
                        'sold' => 'Terjual',
                        'disabled' => 'Nonaktif',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'booked' => 'warning',
                        'sold' => 'gray',
                        'disabled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Kavling'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
