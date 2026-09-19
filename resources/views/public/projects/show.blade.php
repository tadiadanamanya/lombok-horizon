@extends('public.layouts.app')
@section('content')
<x-public.hero
    tagline="Lihat Detail Kavling"
    title="{{$project->nama}}"
    subtitle="{{$project->lokasi}}"
    buttonText="Ajukan Pertanyaan"
    buttonUrl="#"
    x-on:click="$dispatch('open-inquiry-modal', { projectId: {{ $project->id }} })"
/>

<section class="py-16" x-data="projectShow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-[1fr_350px]">
            <!-- Project Info -->
            <div>
                <h1 class="text-4xl font-bold mb-4">{{ $project->nama }}</h1>
                <p class="text-lg text-muted-foreground mb-6">{{ $project->lokasi }}</p>

                <div class="space-y-6">
                    <div class="bg-paper rounded-xl p-6">
                        <h3 class="text-xl font-semibold mb-4">Deskripsi Proyek</h3>
                        <p class="text-muted-foreground leading-relaxed">
                            {!! nl2br(e($project->deskripsi)) !!}
                        </p>
                    </div>

                    <div class="bg-paper rounded-xl p-6">
                        <h3 class="text-xl font-semibold mb-4">Informasi Kavling</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Total Kavling</span>
                                <span class="font-medium">{{ $project->kavling_total }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Kavling Tersedia</span>
                                <span class="font-medium text-success-dark">{{ $project->kavling_available }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Kavling Terjual</span>
                                <span class="font-medium">{{ $project->kavling_sold }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-xl font-semibold mb-4">Gallery</h3>
                        <div id="project-gallery" class="grid gap-4 sm:grid-cols-2">
                            @if($project->images->isEmpty())
                                <div class="col-span-2 bg-paper rounded-lg h-48 flex items-center justify-center">
                                    <p class="text-muted-foreground">Belum ada gambar untuk proyek ini</p>
                                </div>
                            @else
                                @foreach($project->images as $image)
                                    <div class="rounded-lg overflow-hidden">
                                        <img src="{{ asset('storage/'.$image->image_path) }}" alt="Galeri {{$project->nama}}" class="w-full h-48 object-cover">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="sticky top-16">
                <div class="space-y-6">
                    <!-- Map -->
                    <div class="bg-paper rounded-xl p-6">
                        <h3 class="text-xl font-semibold mb-4">Peta Proyek</h3>
                        <div id="project-map" class="leaflet-container rounded-lg"></div>
                    </div>

                    <!-- Kavling List -->
                    <div class="bg-paper rounded-xl p-6">
                        <h3 class="text-xl font-semibold mb-4">Daftar Kavling</h3>
                        <div id="kavling-list" class="space-y-4">
                            <!-- Loading skeleton -->
                            <template x-if="kavlings.length === 0">
                                <div class="p-4 bg-hairline rounded-lg animate-pulse">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-hairline rounded"></div>
                                        <div class="flex-1 space-y-1">
                                            <div class="h-4 bg-hairline rounded w-2/3"></div>
                                            <div class="h-2 bg-hairline rounded w-1/2"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Inquiry CTA -->
                    <div class="bg-paper rounded-xl p-6 text-center">
                        <h3 class="text-xl font-semibold mb-4">Siap Investasi?</h3>
                        <p class="text-muted-foreground mb-4">
                            Hubungi kami sekarang untuk informasi lebih lanjut atau untuk menjadwalkan kunjungan lapangan.
                        </p>
                        <button
                            type="button"
                            class="w-full px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors flex items-center justify-center gap-2"
                            x-on:click="$dispatch('open-inquiry-modal', { projectId: {{ $project->id }} })"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Ajukan Pertanyaan
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('projectShow', () => ({
            projectSlug: @js($project->slug),
            projectId: {{ $project->id }},
            kavlings: [],
            map: null,

            init() {
                this.loadKavlings();
                this.initMap();
            },

            async loadKavlings() {
                try {
                    const response = await fetch(`/api/v1/projects/${this.projectSlug}/kavlings`);
                    const data = await response.json();
                    this.kavlings = data.data || [];
                    this.renderKavlingList();
                } catch (error) {
                    console.error('Error loading kavlings:', error);
                }
            },

            initMap() {
                // CDN bisa lambat/gagal: tunggu L terbatas, lalu tampilkan pesan
                // eksplisit agar kegagalan tidak berupa peta kosong yang silent.
                if (typeof L === 'undefined') {
                    this.mapRetries = (this.mapRetries || 0) + 1;
                    if (this.mapRetries < 50) {
                        setTimeout(() => this.initMap(), 200);
                        return;
                    }
                    document.getElementById('project-map').innerHTML =
                        '<p class="text-center text-muted-foreground py-8">Peta gagal dimuat. Periksa koneksi lalu muat ulang halaman.</p>';
                    return;
                }

                // Initialize Leaflet map
                this.map = L.map('project-map').setView([-8.65, 116.3], 10);

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(this.map);

                this.loadMapGeoJSON();
            },

            async loadMapGeoJSON() {
                // PRD §5.1.3: poligon kavling + batas proyek dari GeoJSON.
                try {
                    const response = await fetch(`/api/v1/projects/${this.projectSlug}/geojson`);
                    const data = await response.json();
                    const features = data.features || [];

                    const statusColor = {
                        available: '#059669',
                        booked: '#D97706',
                        sold: '#64748B',
                        disabled: '#DC2626',
                    };
                    const statusLabel = {
                        available: 'Tersedia',
                        booked: 'Dibooking',
                        sold: 'Terjual',
                        disabled: 'Nonaktif',
                    };

                    const layers = [];
                    features.forEach((feature) => {
                        if (!feature.geometry) return;

                        const props = feature.properties || {};
                        const isBoundary = props.kind === 'boundary';
                        const color = isBoundary ? '#111111' : (statusColor[props.status] || '#DC2626');

                        const layer = L.geoJSON(feature, {
                            style: { color, weight: 2, fillOpacity: isBoundary ? 0.05 : 0.2 },
                        });

                        if (isBoundary) {
                            layer.bindPopup(`Batas Proyek {{ $project->nama }}`);
                        } else {
                            const harga = props.harga ? 'Rp ' + Number(props.harga).toLocaleString('id-ID') : 'Harga belum ditetapkan';
                            const luas = props.luas_m2 ? props.luas_m2 + ' m²' : 'Luas tidak tersedia';
                            layer.bindPopup(
                                `<b>Kavling ${props.nomor ?? ''}</b><br>${luas}<br>${harga}<br>Status: ${statusLabel[props.status] ?? props.status ?? ''}`
                            );
                        }

                        layer.addTo(this.map);
                        layers.push(layer);
                    });

                    if (layers.length > 0) {
                        this.map.fitBounds(L.featureGroup(layers).getBounds(), { padding: [16, 16] });
                    }
                } catch (error) {
                    console.error('Error loading map GeoJSON:', error);
                }
            },

            renderKavlingList() {
                const container = document.getElementById('kavling-list');
                if (!container) return;

                // Get the template element for loading skeleton
                const skeletonTemplate = container.querySelector('template[x-if]');
                
                if (this.kavlings.length === 0) {
                    // Show loading skeleton, hide any existing kavling content
                    skeletonTemplate.innerHTML = `
                        <div class="p-4 bg-hairline rounded-lg animate-pulse">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-hairline rounded"></div>
                                <div class="flex-1 space-y-1">
                                    <div class="h-4 bg-hairline rounded w-2/3"></div>
                                    <div class="h-2 bg-hairline rounded w-1/2"></div>
                                </div>
                            </div>
                        </div>
                    `;
                    // Remove any existing kavling elements
                    const kavlingElements = Array.from(container.children).filter(
                        child => child !== skeletonTemplate && child.tagName === 'DIV'
                    );
                    kavlingElements.forEach(el => el.remove());
                    return;
                }

                // Hide loading skeleton
                skeletonTemplate.innerHTML = '';

                // Create and append kavling elements
                this.kavlings.forEach(kavling => {
                    const statusMap = {
                        available: ['Tersedia', 'bg-success/10 text-success-dark'],
                        booked: ['Dibooking', 'bg-warning/10 text-warning-dark'],
                        sold: ['Terjual', 'bg-sold/10 text-sold-dark'],
                        disabled: ['Nonaktif', 'bg-error/10 text-error-dark'],
                    };
                    const [statusLabel, statusClass] = statusMap[kavling.status] ?? [kavling.status, 'bg-error/10 text-error-dark'];

                    const element = document.createElement('div');
                    element.className = 'p-4 bg-white rounded-lg hover:shadow-sm transition-shadow border';
                    element.innerHTML = `
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold">Kavling ${kavling.nomor}</h4>
                            <span class="px-2 py-1 text-xs rounded ${statusClass}">
                                ${statusLabel}
                            </span>
                        </div>
                        <div class="text-sm text-muted-foreground mb-2">
                            ${kavling.luas_m2 ? kavling.luas_m2 + ' m²' : 'Luas tidak tersedia'}
                        </div>
                        <div class="text-sm font-medium mb-2">
                            ${kavling.harga ? 'Rp ' + kavling.harga.toLocaleString('id-ID') : 'Harga belum ditetapkan'}
                        </div>
                        ${kavling.status === 'available' ? '<button type="button" onclick="window.dispatchEvent(new CustomEvent(\'open-inquiry-modal\', { detail: { projectId: ' + this.projectId + ', kavlingId: ' + kavling.id + ' } }))" class="mt-2 w-full px-4 py-2 bg-primary text-white text-sm rounded-lg font-medium hover:bg-primary/90 transition-colors">Minta Info</button>' : ''}
                    `;
                    container.appendChild(element);
                });
            }
        }));
    });
</script>
@endsection
