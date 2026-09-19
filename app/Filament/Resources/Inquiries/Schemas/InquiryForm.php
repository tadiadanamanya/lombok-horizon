<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use App\Models\Inquiry;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->required(),

                Select::make('kavling_id')
                    ->label('Kavling')
                    ->relationship('kavling', 'nomor')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Select::make('project_id')
                    ->label('Proyek')
                    ->relationship('project', 'nama')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('ref_code')
                    ->label('Kode Referensi')
                    ->helperText('Terisi otomatis')
                    ->disabled()
                    ->dehydrated()
                    ->default(fn () => Inquiry::nextRefCode())
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'baru' => 'Baru',
                        'dihubungi' => 'Dihubungi',
                        'deal' => 'Deal',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('baru')
                    ->required(),
            ]);
    }
}
