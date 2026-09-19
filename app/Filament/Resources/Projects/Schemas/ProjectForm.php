<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Forms\Components\GeoJSONInput;
use App\Filament\Forms\Components\MapPreviewField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Proyek')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug((string) $state))),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('lokasi')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(255),

                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->columnSpanFull(),

                FileUpload::make('thumbnail_path')
                    ->label('Thumbnail')
                    ->image()
                    ->maxSize(5120)
                    ->disk('public')
                    ->directory('thumbnails')
                    ->columnSpanFull(),

                GeoJSONInput::make('batas_proyek')
                    ->label('Batas Proyek (GeoJSON)')
                    ->columnSpanFull(),

                MapPreviewField::for('data.batas_proyek')
                    ->columnSpanFull(),
            ]);
    }
}
