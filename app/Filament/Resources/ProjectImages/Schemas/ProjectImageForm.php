<?php

namespace App\Filament\Resources\ProjectImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),

                FileUpload::make('image_path')
                    ->label('Gambar')
                    ->required()
                    ->image()
                    ->maxSize(10240) // 10MB
                    ->imageEditor()
                    ->imageCropper()
                    ->directory('project-images')
                    ->visibility('public'),

                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->type('number')
                    ->default(0)
                    ->required(),
            ]);
    }
}
