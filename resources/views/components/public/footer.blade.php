<footer class="border-t border-hairline bg-paper">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between pb-12 pt-16">
            <!-- Logo & Description -->
            <div class="flex flex-col items-center md:text-left md:items-start space-y-4 md:space-y-0">
                <div class="flex items-center space-x-3">
                    <span class="flex-shrink-0">
                        <img class="h-8 w-auto" src="/logo.svg" alt="Lombok Horizon Logo">
                    </span>
                    <div class="text-xl font-bold text-ink">
                        Lombok Horizon
                    </div>
                </div>
                <p class="text-center text-muted-foreground max-w-md md:text-left">
                    Menyediakan kavling premium di Lombok dengan view pantai indah dan fasilitas lengkap untuk investasi properti Anda.
                </p>
                <div class="flex flex-col md:flex-row md:space-x-6 space-y-4 md:space-y-0">
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', \App\Models\SiteSetting::get('whatsapp_number', '6281234567890')) }}"
                       class="flex items-center px-3 py-1.5 text-sm font-medium text-muted-foreground hover:text-ink transition-colors"
                       target="_blank"
                       rel="noopener noreferrer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                        Hubungi via WhatsApp
                    </a>
                    <a href="mailto:{{ \App\Models\SiteSetting::get('email', 'info@lombokhorizon.test') }}"
                       class="flex items-center px-3 py-1.5 text-sm font-medium text-muted-foreground hover:text-ink transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ \App\Models\SiteSetting::get('email', 'info@lombokhorizon.test') }}
                    </a>
                </div>
            </div>

            <!-- Links -->
            <div class="hidden md:flex md:flex-col md:items-start md:space-y-4 md:mt-0 mt-8 space-x-8">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-ink">Perusahaan</p>
                    <a href="/about" class="text-muted-foreground hover:text-ink transition-colors">Tentang Kami</a>
                </div>
                <div class="space-y-2">
                    <p class="text-sm font-medium text-ink">Properti</p>
                    <a href="/projects" class="text-muted-foreground hover:text-ink transition-colors">Lihat Semua Kavling</a>
                </div>
                @php
                    $socialLinks = collect(explode("\n", (string) \App\Models\SiteSetting::get('social_links', '')))
                        ->map(fn ($line) => array_map('trim', explode('|', $line, 2)))
                        ->filter(fn ($parts) => count($parts) === 2 && $parts[0] !== '' && filter_var($parts[1], FILTER_VALIDATE_URL));
                @endphp
                @if($socialLinks->isNotEmpty())
                <div class="space-y-2">
                    <p class="text-sm font-medium text-ink">Ikuti Kami</p>
                    @foreach($socialLinks as [$label, $url])
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="block text-muted-foreground hover:text-ink transition-colors">{{ $label }}</a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <div class="border-t border-hairline pt-10">
            <div class="flex flex-col items-center md:flex-row md:justify-between pb-6">
                <span class="text-sm text-muted-foreground">
                    &copy; {{ date('Y') }} Lombok Horizon. Hak cipta dilindungi.
                </span>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="/about" class="text-muted-foreground hover:text-ink transition-colors">Tentang Kami</a>
                    <a href="/projects" class="text-muted-foreground hover:text-ink transition-colors">Properti</a>
                </div>
            </div>
        </div>
    </div>
</footer>