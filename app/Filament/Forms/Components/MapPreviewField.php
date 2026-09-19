<?php

namespace App\Filament\Forms\Components;

use Filament\Schemas\Components\View;

/**
 * Preview peta read-only untuk kolom GeoJSON di form admin.
 *
 * Safety net visual (PRD §14): membaca textarea GeoJSON sibling
 * secara live via JS dan me-render poligonnya di Leaflet.
 */
class MapPreviewField extends View
{
    /**
     * @param  string  $targetStatePath  State path textarea GeoJSON, mis. "data.koordinat_bidang".
     *                                   Dipakai langsung sebagai ID DOM (Filament memakai titik sebagai pemisah).
     */
    public static function for(string $targetStatePath): static
    {
        $static = static::make('filament.forms.components.map-preview-field');

        $static->viewData([
            'targetId' => $targetStatePath,
        ]);

        return $static;
    }
}
