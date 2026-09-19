<?php

namespace App\Filament\Resources\Inquiries\Tables;

use App\Models\Inquiry;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class InquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable(),

                TextColumn::make('kavling.nomor')
                    ->label('Kavling')
                    ->searchable(),

                TextColumn::make('project.nama')
                    ->label('Proyek')
                    ->searchable(),

                TextColumn::make('ref_code')
                    ->label('Kode Referensi')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'baru' => 'Baru',
                        'dihubungi' => 'Dihubungi',
                        'deal' => 'Deal',
                        'ditolak' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'baru' => 'blue',
                        'dihubungi' => 'warning',
                        'deal' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'baru' => 'Baru',
                        'dihubungi' => 'Dihubungi',
                        'deal' => 'Deal',
                        'ditolak' => 'Ditolak',
                    ]),

                SelectFilter::make('project_id')
                    ->label('Proyek')
                    ->relationship('project', 'nama')
                    ->searchable(),

                Filter::make('tanggal')
                    ->label('Tanggal')
                    ->form([
                        DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->placeholder('Pilih tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal'],
                                fn (Builder $query, $date) => $query->whereDate('created_at', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        return $data['tanggal']
                            ? [Indicator::make('Tanggal: '.$data['tanggal'])]
                            : [];
                    }),
            ])
            ->recordActions([
                Action::make('updateStatus')
                    ->label('Perbarui Status')
                    ->icon('heroicon-m-arrow-right')
                    ->color('primary')
                    ->action(function (Inquiry $record) {
                        $statuses = ['baru', 'dihubungi', 'deal', 'ditolak'];
                        $currentIndex = array_search($record->status, $statuses);
                        $nextIndex = min($currentIndex + 1, count($statuses) - 1);
                        $record->status = $statuses[$nextIndex];
                        $record->save();
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
