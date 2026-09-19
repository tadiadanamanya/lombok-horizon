@extends('public.layouts.app')
@section('content')
<div x-data="homePage">
<x-public.hero
    tagline="Nikmati Keindahan Alam Lombok"
    title="{{ \App\Models\SiteSetting::get('hero_title', 'Lombok Horizon') }}"
    subtitle="{{ \App\Models\SiteSetting::get('hero_subtitle', 'Investasi Properti Premium dengan View Pantai Indah') }}"
    buttonText="Lihat Properti"
    buttonUrl="{{ route('projects.index') }}"
/>

<section class="py-16 bg-paper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12">Kenapa Memilih Lombok Horizon?</h2>
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3 text-center">
            <div>
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4m0 0a1 1 0 011-1h3m-3 4a1 1 0 01-1 1h-3m-4 0a1 1 0 001-1v-3m0 0a1 1 0 011-1h3"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Lokasi Strategis</h3>
                <p class="text-muted-foreground">
                    Dekat dengan pantai, bandara, dan fasilitas publik sambil tetap menyajikan suasana tenang dan alam yang indah.
                </p>
            </div>
            <div>
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0 10c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0-6c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Investasi Aman</h3>
                <p class="text-muted-foreground">
                    Nilai properti di Lombok konsisten naik seiring waktu dengan pertumbuhan pariwisata dan infrastruktur yang pesat.
                </p>
            </div>
            <div>
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Fasilitas Lengkap</h3>
                <p class="text-muted-foreground">
                    Klub house, kolam renang, taman lanskap, dan sistem keamanan 24 jam untuk kenyamanan dan keamanan penghuninya.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12">Properti Terbaru</h2>

        <!-- Projects Grid -->
        <div id="projects-grid" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Loading skeleton -->
            <div class="col-span-3 bg-paper rounded-xl p-6 animate-pulse">
                <div class="space-y-4">
                    <div class="h-8 bg-hairline rounded"></div>
                    <div class="h-4 bg-hairline rounded w-2/3"></div>
                    <div class="h-4 bg-hairline rounded w-1/2"></div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <button id="load-more-btn"
                    class="px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    x-on:click="loadMoreProjects()"
                    x-text="loadMoreText">
                Lihat Lebih Banyak
            </button>
        </div>
    </div>
</section>

<section class="py-16 bg-paper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12">Siap Memulai Investasi Anda?</h2>
        <p class="text-center text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
            Hubungi tim kami sekarang untuk konsultasi gratis dan penawaran khusus.
        </p>
        <div class="flex flex-col sm:flex-row sm:space-x-4 justify-center">
            <button
                class="flex-1 sm:flex-1 px-6 py-4 bg-success text-white rounded-lg font-medium hover:bg-success/90 transition-colors flex items-center justify-center gap-2"
                x-on:click="$dispatch('open-inquiry-modal')"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path>
                </svg>
                Hubungi via WhatsApp
            </button>
            <button
                class="flex-1 sm:flex-1 px-6 py-4 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors"
                x-on:click="$dispatch('open-inquiry-modal')"
            >
                Kirim Pertanyaan
            </button>
        </div>
    </div>
</section>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('homePage', () => ({
            projects: [],
            currentPage: 1,
            perPage: 6,
            hasMore: true,
            isLoading: false,
            loadMoreText: 'Lihat Lebih Banyak',

            init() {
                this.loadProjects();
            },

            async loadProjects() {
                if (this.isLoading) return;
                this.isLoading = true;
                try {
                    const response = await fetch(`/api/v1/projects?page=${this.currentPage}&per_page=${this.perPage}`);
                    const data = await response.json();

                    if (this.currentPage === 1) {
                        this.projects = data.data || [];
                    } else {
                        this.projects = [...this.projects, ...(data.data || [])];
                    }

                    this.hasMore = data.current_page < data.last_page;
                    this.loadMoreText = this.hasMore ? 'Lihat Lebih Banyak' : 'Tidak Ada Lagi';

                    // Render projects
                    this.renderProjects();
                } catch (error) {
                    console.error('Error loading projects:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            renderProjects() {
                const grid = document.getElementById('projects-grid');
                if (!grid) return;

                // Clear loading skeleton
                grid.innerHTML = '';

                if (this.projects.length === 0) {
                    grid.innerHTML = '<p class="col-span-3 text-center text-muted-foreground py-8">Belum ada properti yang tersedia.</p>';
                    return;
                }

                this.projects.forEach(project => {
                    const card = document.createElement('div');
                    card.className = 'bg-white rounded-xl overflow-hidden hover:shadow-sm transition-shadow';
                    card.innerHTML = `
                        <div class="relative">
                            <img
                                src="${project.thumbnail_url || '/images/default-project.svg'}"
                                alt="${project.nama}"
                                class="w-full h-48 object-cover"
                            >
                            <span class="absolute top-3 left-3 px-2 py-1 bg-primary/90 text-white text-xs rounded">
                                ${project.kavling_available}/${project.kavling_total} Kavling
                            </span>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2 line-clamp-2">${project.nama}</h3>
                            <p class="text-muted-foreground mb-3 line-clamp-2">${project.lokasi}</p>
                            <div class="flex justify-between items-center">
                                <a href="/projects/${project.slug}"
                                   class="text-sm font-medium text-primary hover:text-primary/80">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            },

            async loadMoreProjects() {
                if (!this.hasMore || this.isLoading) return;

                this.currentPage++;
                await this.loadProjects();
            }
        }));
    });
</script>
</div>
@endsection
