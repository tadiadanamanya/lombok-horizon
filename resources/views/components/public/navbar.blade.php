<nav class="bg-white border-b border-hairline">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between py-4">
            <div class="flex items-center flex-shrink-0 space-x-3 rtl:space-x-reverse">
                <span class="flex-shrink-0">
                    <img class="h-8 w-auto" src="/logo.svg" alt="Lombok Horizon Logo">
                </span>
                <div class="hidden md:block">
                    <div class="text-xl font-bold text-ink">
                        Lombok Horizon
                    </div>
                </div>
            </div>

            <div class="hidden md:block md:ml-10 md:flex md:items-center md:space-x-8 rtl:space-x-reverse">
                <x-public.nav-link href="/" :is-active="request()->is('/')">Beranda</x-public.nav-link>
                <x-public.nav-link href="/projects" :is-active="request()->is('projects*')">Properti</x-public.nav-link>
                <x-public.nav-link href="/about" :is-active="request()->is('about')">Tentang Kami</x-public.nav-link>
            </div>

            <div class="hidden md:block">
                <div class="flex items-center space-x-4">
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', \App\Models\SiteSetting::get('whatsapp_number', '6281234567890')) }}"
                       class="flex items-center px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
                       target="_blank"
                       rel="noopener noreferrer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                        </svg>
                        WhatsApp
                    </a>
                </div>
            </div>

            <div class="flex items-center md:hidden">
                <!-- Mobile menu button -->
                <button type="button"
                        class="flex items-center px-2 py-2 rounded-md text-muted-foreground hover:text-ink hover:bg-paper transition-colors"
                        @click="toggleMenu()"
                        aria-label="Buka atau tutup menu navigasi"
                        :aria-expanded="isMenuOpen"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isMenuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden" :class="{'hidden': !isMenuOpen}">
        <div class="pt-2 pb-3 space-y-1">
            <x-public.nav-link href="/" :is-active="request()->is('/')" class="block px-3 py-2 rounded-md text-base font-medium text-muted-foreground hover:text-ink hover:bg-paper">Beranda</x-public.nav-link>
            <x-public.nav-link href="/projects" :is-active="request()->is('projects*')" class="block px-3 py-2 rounded-md text-base font-medium text-muted-foreground hover:text-ink hover:bg-paper">Properti</x-public.nav-link>
            <x-public.nav-link href="/about" :is-active="request()->is('about')" class="block px-3 py-2 rounded-md text-base font-medium text-muted-foreground hover:text-ink hover:bg-paper">Tentang Kami</x-public.nav-link>
        </div>
    </div>
</nav>