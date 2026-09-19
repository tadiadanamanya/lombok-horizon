@extends('public.layouts.app')
@section('content')
<x-public.hero
    tagline="Jelajah Pilihan Kavling Kami"
    title="Daftar Properti"
    subtitle="Lihat semua kavling yang tersedia di Lombok Horizon"
    buttonText="Lihat Semua Kavling"
    buttonUrl="{{ route('projects.index') }}"
/>

<section class="py-16 bg-paper" x-data="projectsIndex">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-12">
            <h2 class="text-3xl font-bold">Daftar Properti</h2>
            <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                <x-public.kavling-filter
                    :projects="$projects"
                    :selected-project-id="$selectedProjectId ?? ''"
                />
            </div>
        </div>

        <!-- Projects Grid -->
        <div id="projects-grid" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Loading skeleton -->
            <template x-for="n in 6" :key="n">
                <div class="bg-paper rounded-xl p-6 animate-pulse">
                    <div class="space-y-4">
                        <div class="h-8 bg-hairline rounded"></div>
                        <div class="h-4 bg-hairline rounded w-2/3"></div>
                        <div class="h-4 bg-hairline rounded w-1/2"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Load More Button -->
        <div class="mt-8 text-center">
            <button
                id="load-more-btn"
                class="px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors"
                x-on:click="loadMoreProjects()"
                x-text="loadMoreText"
                :disabled="isLoading"
            >
                Lihat Lebih Banyak
            </button>
        </div>

        <!-- Empty State -->
        <template x-if="projects.length === 0 && !isLoading">
            <p class="col-span-3 text-center text-muted-foreground py-12">
                Belum ada kavling yang tersedia untuk filter yang dipilih.
            </p>
        </template>
    </div>
</section>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('projectsIndex', () => ({
            projects: [],
            selectedProjectId: '',
            currentPage: 1,
            perPage: 12,
            hasMore: true,
            isLoading: false,
            loadMoreText: 'Lihat Lebih Banyak',

            init() {
                this.loadProjects();

                // Listen for filter changes
                document.addEventListener('project-filter-changed', (e) => {
                    this.selectedProjectId = e.detail.projectId;
                    this.currentPage = 1;
                    this.hasMore = true;
                    this.loadProjects();
                });
            },

            async loadProjects() {
                if (this.isLoading) return;
                this.isLoading = true;

                try {
                    const params = new URLSearchParams({
                        page: this.currentPage,
                        per_page: this.perPage
                    });

                    if (this.selectedProjectId) {
                        params.append('project_id', this.selectedProjectId);
                    }

                    const response = await fetch(`/api/v1/projects?${params.toString()}`);
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

                // Clear loading skeletons
                grid.innerHTML = '';

                if (this.projects.length === 0) {
                    const message = this.selectedProjectId
                        ? 'Belum ada kavling yang tersedia untuk proyek yang dipilih.'
                        : 'Belum ada kavling yang tersedia.';

                    grid.innerHTML = `<p class="col-span-3 text-center text-muted-foreground py-12">${message}</p>`;
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
            },

            updateProjectFilter(projectId) {
                this.selectedProjectId = projectId;
                this.currentPage = 1;
                this.hasMore = true;
                this.loadProjects();
            }
        }));
    });
</script>
@endsection
