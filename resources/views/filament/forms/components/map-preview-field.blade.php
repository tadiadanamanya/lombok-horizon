{{-- Preview + gambar poligon untuk kolom GeoJSON (safety net visual, PRD §14). --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<div
    x-data="geojsonMapPreview(@js($targetId))"
    x-init="init()"
    wire:key="map-preview-{{ str_replace('.', '-', $targetId) }}"
    class="fi-fo-map-preview-field"
>
    <div x-ref="map" class="w-full rounded-lg border border-gray-200" style="height: 320px; min-height: 320px;"></div>
    <p x-show="error" x-text="error" class="mt-2 text-sm text-red-600"></p>
    <p x-show="!error" x-text="helperText" class="mt-2 text-sm text-gray-500"></p>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
    function geojsonMapPreview(targetId) {
        return {
            map: null,
            drawnItems: null,
            error: '',
            helperText: "Klik ikon poligon untuk menggambar'area",

            init() {
                const component = this;

                if (typeof L === 'undefined') {
                    this.error = 'Pustaka peta gagal dimuat. Periksa koneksi lalu muat ulang.';
                    return;
                }

                // Idempotent: init bisa jalan ulang pasca-morph Livewire.
                // Buang instance lama agar tak ada map yatim/ganda.
                if (window.__geojsonMaps?.[targetId]) {
                    window.__geojsonMaps[targetId].remove();
                    delete window.__geojsonMaps[targetId];
                }

                this.map = L.map(this.$refs.map).setView([-8.65, 116.3], 10);
                window.__geojsonMaps ??= {};
                window.__geojsonMaps[targetId] = this.map;
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(this.map);

                // Initialize Leaflet.draw
                this.drawnItems = new L.FeatureGroup();
                this.map.addLayer(this.drawnItems);

                const drawControl = new L.Control.Draw({
                    edit: {
                        featureGroup: this.drawnItems,
                        remove: true // Enable remove
                    },
                    draw: {
                        polygon: {
                            allowIntersection: false, // Prevent holes
                            shapeOptions: {
                                color: '#059669', // --available color from design system
                                weight: 2
                            }
                        },
                        polyline: false,
                        rectangle: false,
                        circle: false,
                        marker: false,
                        circlemarker: false
                    }
                });
                this.map.addControl(drawControl);

                // Handle drawn shapes
                this.map.on(L.Draw.Event.CREATED, (event) => {
                    this.writeToTextarea(event.layer);
                });

                this.map.on(L.Draw.Event.EDITED, (e) => {
                    e.layers.eachLayer((layer) => {
                        this.writeToTextarea(layer);
                    });
                });

                this.map.on(L.Draw.Event.DELETED, () => {
                    const target = this.resolveTarget();
                    if (target && this.drawnItems.getLayers().length === 0) {
                        target.value = '';
                        target.dispatchEvent(new Event('input'));
                    }
                });

                this.render();

                const target = this.resolveTarget();
                target?.addEventListener('input', () => this.render());

                // Container sempat 0px saat CSS admin belum final;
                // paksa Leaflet mengukur ulang setelah paint.
                setTimeout(() => this.map.invalidateSize(), 100);

                // Re-sync setiap selesai morph Livewire (mis. setelah save):
                // hitung ulang preview dari nilai textarea terkini.
                // Register sekali per elemen agar hook tidak menumpuk.
                if (typeof Livewire !== 'undefined' && !this.$el.dataset.morphHook) {
                    this.$el.dataset.morphHook = '1';
                    Livewire.hook('morph.updated', () => component.render());
                }
            },

            // Resolve textarea target secara berlapis: ID state path penuh
            // ("data.koordinat_bidang"), nama field tanpa prefix, lalu
            // atribut wire:model. Warn bila gagal agar tidak silent.
            resolveTarget() {
                const candidates = [
                    () => document.getElementById(targetId),
                    () => document.getElementById(targetId.replace(/^data\./, '')),
                    () => document.querySelector('[wire\\:model="' + targetId + '"]'),
                ];

                for (const find of candidates) {
                    const el = find();
                    if (el) return el;
                }

                console.warn('[map-preview] target textarea tidak ditemukan untuk: ' + targetId);
                return null;
            },

            writeToTextarea(layer) {
                const geojson = layer.toGeoJSON();
                const geometry = geojson.type === 'FeatureCollection'
                    ? geojson.features?.[0]?.geometry
                    : geojson.geometry;
                if (!geometry) return;
                const target = this.resolveTarget();
                if (target) {
                    target.value = JSON.stringify(geometry, null, 2);
                    target.dispatchEvent(new Event('input')); // Trigger Livewire update
                }
            },

            normalizeInput(raw) {
                if (!raw) return null;

                let parsed;
                try {
                    parsed = JSON.parse(raw);
                } catch (e) {
                    return null; // Invalid JSON
                }

                // Handle different GeoJSON formats
                if (parsed.type === 'Feature' || parsed.type === 'FeatureCollection') {
                    // Already a Feature or FeatureCollection - extract geometry
                    if (parsed.type === 'Feature') {
                        return parsed.geometry;
                    } else if (parsed.type === 'FeatureCollection' && parsed.features.length > 0) {
                        // For FeatureCollection, use the first feature's geometry
                        return parsed.features[0].geometry;
                    }
                } else if (parsed.type && parsed.coordinates) {
                    // Already a geometry object
                    return parsed;
                } else if (Array.isArray(parsed)) {
                    // Might be coordinates array - wrap as Polygon
                    return {
                        type: 'Polygon',
                        coordinates: parsed
                    };
                }

                return null;
            },

            render() {
                const target = this.resolveTarget();
                const raw = target?.value.trim() ?? '';

                // Clear existing layers
                this.drawnItems.clearLayers();

                if (!raw) {
                    this.error = '';
                    this.helperText = "Klik ikon poligon untuk menggambar'area";
                    return;
                }

                const geometry = this.normalizeInput(raw);
                if (!geometry || (geometry.type !== 'Polygon' && geometry.type !== 'MultiPolygon') || !geometry.coordinates) {
                    this.error = 'Isi dengan GeoJSON Polygon/MultiPolygon yang valid.';
                    this.helperText = '';
                    return;
                }

                try {
                    // Leaflet menangani GeoJSON [lng, lat] secara native; jangan konversi manual.
                    const feature = { type: 'Feature', properties: {}, geometry };
                    const layer = L.geoJSON(feature, {
                        style: { color: '#059669', weight: 2 },
                    });

                    // Flatten ke drawnItems agar vertex bisa diedit Leaflet.draw.
                    layer.eachLayer((subLayer) => this.drawnItems.addLayer(subLayer));

                    const bounds = this.drawnItems.getBounds();
                    if (bounds.isValid()) {
                        this.map.fitBounds(bounds, { padding: [16, 16] });
                        setTimeout(() => this.map.invalidateSize(), 100);
                    }
                    this.error = '';
                    this.helperText = 'Gambar dapat diedit - geser vertex untuk mengubah bentuk';
                } catch (e) {
                    this.error = 'Geometri tidak dapat di-render.';
                    this.helperText = '';
                }
            },
        };
    }
</script>