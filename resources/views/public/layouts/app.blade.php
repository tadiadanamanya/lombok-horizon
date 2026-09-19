<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lombok Horizon - Properti Premium di Lombok</title>
    <meta name="description" content="Lombok Horizon menawarkan kavling premium di Lombok dengan view pantai indah dan fasilitas lengkap. Investasi properti Anda di sini!">
    <meta name="keywords" content="Lombok Horizon, properti Lombok, kavling untuk dijual, investasi properti, Bali">
    <meta name="author" content="Lombok Horizon Development Team">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://lombokhorizon.id/">
    <meta property="og:title" content="Lombok Horizon - Properti Premium di Lombok">
    <meta property="og:description" content="Lombok Horizon menawarkan kavling premium di Lombok dengan view pantai indah dan fasilitas lengkap. Investasi properti Anda di sini!">
    <meta property="og:image" content="https://lombokhorizon.id/og-image.jpg">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://lombokhorizon.id/">
    <meta name="twitter:title" content="Lombok Horizon - Properti Premium di Lombok">
    <meta name="twitter:description" content="Lombok Horizon menawarkan kavling premium di Lombok dengan view pantai indah dan fasilitas lengkap. Investasi properti Anda di sini!">

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- Styles & scripts (Vite build: Tailwind + Plus Jakarta Sans) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet JS (defer; dieksekusi berurutan sesuai posisi tag) -->
    <script defer src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Custom CSS -->
    <style>
        [x-cloak] { display: none !important; }
        .leaflet-container {
            height: 400px;
            width: 100%;
            z-index: 1;
        }
    </style>

</head>
<body class="font-sans antialiased bg-white text-ink">
    <div x-data="{ isMenuOpen: false, toggleMenu() { this.isMenuOpen = !this.isMenuOpen } }">
        <!-- Navbar -->
        <x-public.navbar />

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <x-public.footer />

        <!-- Global inquiry modal (dibuka via $dispatch('open-inquiry-modal')) -->
        <x-public.inquiry-modal />
    </div>
</body>
</html>