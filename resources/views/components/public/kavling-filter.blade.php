<div class="relative w-full" x-data="kavlingFilter">
    <label for="project-filter" class="block text-sm font-medium mb-2 text-ink">
        Filter berdasarkan proyek
    </label>
    <select
        id="project-filter"
        class="w-full px-4 py-3 border border-hairline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
        x-model="selectedProjectId"
        @change="updateFilter"
        aria-label="Pilih proyek untuk memfilter kavling"
    >
        <option value="">Semua Proyek</option>
        @foreach($projects as $project)
        <option value="{{ $project->id }}">{{ $project->nama }}</option>
        @endforeach
    </select>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('kavlingFilter', () => ({
            selectedProjectId: '',

            init() {
                // Set initial value if provided
                const initial = @js($selectedProjectId ?? '');
                if (initial) {
                    this.selectedProjectId = initial;
                }
            },

            updateFilter() {
                // Dispatch event to parent component
                const event = new CustomEvent('project-filter-changed', {
                    detail: {
                        projectId: this.selectedProjectId
                    }
                });
                document.dispatchEvent(event);
            }
        }));
    });
</script>