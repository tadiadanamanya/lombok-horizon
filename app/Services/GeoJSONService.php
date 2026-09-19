<?php

namespace App\Services;

use App\Models\Kavling;
use App\Models\Project;

class GeoJSONService
{
    /**
     * Convert a Kavling model to a GeoJSON Feature.
     *
     * @param  Kavling  $kavling
     * @return array
     */
    public function toGeoJsonFeature($kavling)
    {
        // koordinat_bidang bisa berupa array (cast model) atau string JSON.
        $raw = $kavling->koordinat_bidang;
        $coordinates = is_string($raw) ? json_decode($raw, true) : $raw;

        if (! is_array($coordinates)) {
            $coordinates = [];
        }

        // Validate that we have coordinates
        if (empty($coordinates)) {
            // RFC 7946: geometri boleh null; jangan pancarkan titik palsu.
            return [
                'type' => 'Feature',
                'geometry' => null,
                'properties' => [
                    'kind' => 'kavling',
                    'id' => $kavling->id,
                    'nomor' => $kavling->nomor,
                    'luas_m2' => $kavling->luas_m2,
                    'harga' => $kavling->harga,
                    'status' => $kavling->status,
                    'project_id' => $kavling->project_id,
                ],
            ];
        }

        // koordinat_bidang disimpan sebagai objek GeoJSON penuh
        // ({"type": "Polygon", "coordinates": [...]}) — uraikan dulu.
        // Array koordinat mentah tetap didukung.
        $geometryType = 'Polygon'; // Default assumption
        $geomCoordinates = $coordinates;

        if (isset($coordinates['coordinates']) && is_array($coordinates['coordinates'])) {
            $geometryType = is_string($coordinates['type'] ?? null) ? $coordinates['type'] : 'Polygon';
            $geomCoordinates = $coordinates['coordinates'];
        } elseif (isset($coordinates[0][0][0]) && is_array($coordinates[0][0][0])) {
            // MultiPolygon
            $geometryType = 'MultiPolygon';
        } elseif (isset($coordinates[0][0]) && ! is_array($coordinates[0][0])) {
            // LineString
            $geometryType = 'LineString';
        } elseif (isset($coordinates[0]) && ! is_array($coordinates[0])) {
            // Point
            $geometryType = 'Point';
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => $geometryType,
                'coordinates' => $geomCoordinates,
            ],
            'properties' => [
                'kind' => 'kavling',
                'id' => $kavling->id,
                'nomor' => $kavling->nomor,
                'luas_m2' => $kavling->luas_m2,
                'harga' => $kavling->harga,
                'status' => $kavling->status,
                'project_id' => $kavling->project_id,
            ],
        ];
    }

    /**
     * Convert a Project model to a GeoJSON FeatureCollection.
     *
     * PRD §9.4: feature pertama = batas proyek (kind: boundary),
     * diikuti feature tiap kavling (kind: kavling).
     *
     * @param  Project  $project
     * @return array
     */
    public function toGeoJsonCollection($project)
    {
        $features = [];

        $boundary = $this->normalizeGeometry($project->batas_proyek);
        if ($boundary) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => $boundary,
                'properties' => [
                    'kind' => 'boundary',
                    'project' => $project->nama,
                ],
            ];
        }

        foreach ($project->kavlings as $kavling) {
            $features[] = $this->toGeoJsonFeature($kavling);
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    /**
     * Normalisasi nilai geometri (array cast / string JSON) menjadi
     * objek geometri GeoJSON, atau null bila kosong.
     */
    protected function normalizeGeometry(mixed $raw): ?array
    {
        $geometry = is_string($raw) ? json_decode($raw, true) : $raw;

        if (! is_array($geometry) || empty($geometry['coordinates'])) {
            return null;
        }

        return [
            'type' => is_string($geometry['type'] ?? null) ? $geometry['type'] : 'Polygon',
            'coordinates' => $geometry['coordinates'],
        ];
    }
}
