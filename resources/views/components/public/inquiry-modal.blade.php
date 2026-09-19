<div x-data="inquiryModal"
     x-cloak
     x-show="showInquiryModal"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="transform opacity-0 scale-95"
     x-transition:enter-end="transform opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="transform opacity-100 scale-100"
     x-transition:leave-end="transform opacity-0 scale-95"
     @keydown.escape.window="showInquiryModal = false"
     role="dialog"
     aria-modal="true"
     aria-labelledby="inquiry-title"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="relative w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="relative bg-white rounded-lg shadow-sm p-6">
            <!-- Close Button -->
            <button
                type="button"
                class="absolute top-3 right-3 text-muted-foreground hover:text-ink transition-colors"
                @click="showInquiryModal = false"
                aria-label="Tutup modal"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Modal Content -->
            <div class="pt-6">
                <h3 id="inquiry-title" class="text-xl font-bold text-center mb-6">Kirim Pertanyaan Anda</h3>

                <form @submit.prevent="submitInquiry">
                    <div class="space-y-4">
                        <div>
                            <label for="inquiry-nama" class="block text-sm font-medium mb-1">Nama Lengkap</label>
                            <input
                                type="text"
                                id="inquiry-nama"
                                x-model="nama"
                                class="w-full px-4 py-3 border border-hairline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Masukkan nama lengkap Anda"
                                required
                                :disabled="submitting"
                            >
                        </div>

                        <div>
                            <label for="inquiry-phone" class="block text-sm font-medium mb-1">Nomor WhatsApp</label>
                            <input
                                type="tel"
                                id="inquiry-phone"
                                x-model="phone"
                                class="w-full px-4 py-3 border border-hairline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Contoh: 081234567890"
                                inputmode="tel"
                                required
                                :disabled="submitting"
                            >
                        </div>

                        <div x-show="kavlingOptions.length > 0">
                            <label for="inquiry-kavling" class="block text-sm font-medium mb-1">Minat Kavling (opsional)</label>
                            <select
                                id="inquiry-kavling"
                                x-model="kavlingId"
                                class="w-full px-4 py-3 border border-hairline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                :disabled="submitting"
                            >
                                <option value="">Pilih kavling...</option>
                                <template x-for="kavling in kavlingOptions" :key="kavling.id">
                                    <option :value="kavling.id" x-text="kavling.project_nama + ' - Kavling ' + kavling.nomor"></option>
                                </template>
                            </select>
                        </div>

                        <template x-if="error">
                            <div class="bg-error/10 border-l-4 border-error p-4 text-sm text-error-dark" x-text="error"></div>
                        </template>

                        <template x-if="success">
                            <div class="bg-success/10 border-l-4 border-success p-4 text-sm text-success-dark">
                                Terima kasih! Tim kami akan segera menghubungi Anda via WhatsApp.
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 pt-4 border-t border-hairline">
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 transition-colors disabled:opacity-50"
                            :disabled="submitting"
                            x-text="submitting ? 'Mengirim...' : 'Kirim Pertanyaan'"
                        >
                            Kirim Pertanyaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('inquiryModal', () => ({
            showInquiryModal: false,
            nama: '',
            phone: '',
            kavlingId: '',
            projectId: null,
            projectOptions: [],
            kavlingOptions: [],
            submitting: false,
            success: false,
            error: '',

            init() {
                this.loadProjects();

                // Dibuka dari tombol "Minta Info" / CTA di halaman mana pun.
                document.addEventListener('open-inquiry-modal', (e) => {
                    this.error = '';
                    this.success = false;
                    this.projectId = e.detail?.projectId ?? null;
                    this.kavlingId = e.detail?.kavlingId ?? '';
                    this.loadKavlingsForProject();
                    this.showInquiryModal = true;
                });
            },

            async loadProjects() {
                try {
                    const response = await fetch('/api/v1/projects');
                    const data = await response.json();
                    this.projectOptions = data.data || [];
                } catch (error) {
                    console.error('Error loading projects:', error);
                }
            },

            async loadKavlingsForProject() {
                this.kavlingOptions = [];
                if (!this.projectId) return;

                const project = this.projectOptions.find(p => p.id === this.projectId);
                if (!project) return;

                try {
                    const response = await fetch(`/api/v1/projects/${project.slug}/kavlings`);
                    const data = await response.json();
                    this.kavlingOptions = (data.data || []).map(kavling => ({
                        id: kavling.id,
                        nomor: kavling.nomor,
                        project_nama: project.nama,
                    }));
                } catch (error) {
                    console.error('Error loading kavlings:', error);
                }
            },

            async submitInquiry() {
                if (this.submitting) return;

                if (!this.nama.trim() || !this.phone.trim()) {
                    this.error = 'Nama dan nomor WhatsApp wajib diisi';
                    return;
                }

                const phoneRegex = /^(\+62|62)?[\s-]?0?8[1-9][0-9]{7,9}$/;
                if (!phoneRegex.test(this.phone.trim())) {
                    this.error = 'Nomor WhatsApp tidak valid. Gunakan format Indonesia (contoh: 081234567890)';
                    return;
                }

                this.submitting = true;
                this.error = '';

                try {
                    const response = await fetch('/api/v1/inquiries', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            nama: this.nama.trim(),
                            phone: this.phone.trim(),
                            kavling_id: this.kavlingId || null,
                            project_id: this.projectId,
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }

                    const data = await response.json();

                    // PRD §5.1.5: redirect ke WA admin dengan pesan terisi.
                    if (data.data?.redirect_url) {
                        window.location.href = data.data.redirect_url;
                        return;
                    }

                    this.success = true;
                    this.nama = '';
                    this.phone = '';
                    this.kavlingId = '';

                    setTimeout(() => {
                        this.showInquiryModal = false;
                        this.success = false;
                    }, 3000);
                } catch (error) {
                    this.error = error.message;
                } finally {
                    this.submitting = false;
                }
            }
        }));
    });
</script>
