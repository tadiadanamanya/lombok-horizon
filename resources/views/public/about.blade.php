@extends('public.layouts.app')
@section('content')
<div x-data="{}">
<x-public.hero
    tagline="Mengisi Komitmen Kami"
    title="Tentang Lombok Horizon"
    subtitle="Visi, misi, dan nilai yang menggerakkan kami"
    buttonText="Hubungi Kami"
    buttonUrl="#"
    x-on:click="$dispatch('open-inquiry-modal')"
/>

@if(trim((string) \App\Models\SiteSetting::get('about_story', '')) !== '')
<section class="py-16 bg-paper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <h2 class="text-3xl font-bold mb-6">Cerita Kami</h2>
            <p class="text-muted-foreground leading-relaxed whitespace-pre-line">{{ \App\Models\SiteSetting::get('about_story') }}</p>
        </div>
    </div>
</section>
@endif

<section class="py-16 bg-paper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-2">
            <!-- Vision & Mission -->
            <div>
                <h2 class="text-3xl font-bold mb-6">Visi & Misi</h2>

                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold mb-3">Visi</h3>
                        <p class="text-muted-foreground leading-relaxed">
                            Menjadi developer properti terdepan di Lombok yang menciptakan rumah impian dengan standar internasional, sambil mempertahankan kealaman dan budaya lokal yang kaya.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold mb-3">Misi</h3>
                        <ul class="list-disc space-y-2 pl-5 text-muted-foreground">
                            <li>Menyediakan properti berkualitas tinggi dengan desain yang fungsional dan estetis</li>
                            <li>Menciptakan lingkungan yang harmonius antara pembangunan dan alam sekitar</li>
                            <li>Memberikan layanan pelanggan yang luar biasa dari awal hingga pasca penjualan</li>
                            <li>Berdayakan komunitas lokal melalui penciptaan lapangan kerja dan partisipasi aktif</li>
                            <li>Terapkan praktik berkeluan dalam setiap fase pengembangan proyek</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Values -->
            <div>
                <h2 class="text-3xl font-bold mb-6">Nilai Integritas Kami</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="bg-white rounded-xl p-6 hover:shadow-sm transition-shadow border">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0 10c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0-6c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-1">Integritas</h3>
                                <p class="text-sm text-muted-foreground">
                                    Kami berkomitmen untuk selalu beroperasi dengan transparansi, kejujuran, dan standar etika tertinggi dalam setiap transaksi dan interaksi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 hover:shadow-sm transition-shadow border">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-1">Inovasi</h3>
                                <p class="text-sm text-muted-foreground">
                                    Kami terus mencari solusi baru dan pendekatan kreatif untuk meningkatkan kualitas hidup penghuni dan nilai investasi properti.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 hover:shadow-sm transition-shadow border">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0 10c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0-6c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-1">Kepuasan Pelanggan</h3>
                                <p class="text-sm text-muted-foreground">
                                    Kebutuhan dan kepuasan pelanggan adalah prioritas utama kami, dengan layanan yang responsif dan solusi yang sesuai dengan ekspektasi mereka.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-6 hover:shadow-sm transition-shadow border">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11v6a2 2 0 002 2h10a2 2 0 002-2m0-6V9a2 2 0 012-2m0 0V9a2 2 0 00-2-2m2 2h2"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-1">Keberlanjutan</h3>
                                <p class="text-sm text-muted-foreground">
                                    Kami mengintegrasikan prinsip keberlanjutan dalam perencanaan, desain, dan konstruksi proyek untuk menjaga lingkungan bagi generasi mendatang.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</div>
@endsection
