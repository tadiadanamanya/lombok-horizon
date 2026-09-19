<?php

namespace App\Filament\Resources\Kavlings;

use App\Filament\Resources\Kavlings\Pages\CreateKavling;
use App\Filament\Resources\Kavlings\Pages\EditKavling;
use App\Filament\Resources\Kavlings\Pages\ListKavlings;
use App\Filament\Resources\Kavlings\Schemas\KavlingForm;
use App\Filament\Resources\Kavlings\Tables\KavlingsTable;
use App\Models\Kavling;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KavlingResource extends Resource
{
    protected static ?string $model = Kavling::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    public static function form(Schema $schema): Schema
    {
        return KavlingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KavlingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKavlings::route('/'),
            'create' => CreateKavling::route('/create'),
            'edit' => EditKavling::route('/{record}/edit'),
        ];
    }
}
