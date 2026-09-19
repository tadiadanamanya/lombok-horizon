<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Textarea;

/**
 * Textarea GeoJSON dengan validasi longgar (JSON valid saja, PRD §7).
 *
 * State disimpan sebagai array (kolom json + cast model); saat
 * ditampilkan diformat pretty-print agar mudah dibaca admin.
 */
class GeoJSONInput extends Textarea
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->rows(8)
            ->helperText('Tempel GeoJSON Polygon. Contoh: {"type": "Polygon", "coordinates": [[[lng, lat], ...]]}')
            ->rule(static function (): Closure {
                return function (string $attribute, mixed $value, Closure $fail): void {
                    if (blank($value)) {
                        return;
                    }

                    $decoded = is_string($value) ? json_decode($value, true) : $value;

                    if (! is_array($decoded)) {
                        $fail('Isi harus berupa JSON yang valid.');
                    }
                };
            })
            ->formatStateUsing(static fn (mixed $state): ?string => is_array($state)
                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : $state)
            ->dehydrateStateUsing(static function (mixed $state): ?array {
                if (blank($state)) {
                    return null;
                }

                if (is_array($state)) {
                    return $state;
                }

                $decoded = json_decode((string) $state, true);

                return is_array($decoded) ? $decoded : null;
            });
    }
}
