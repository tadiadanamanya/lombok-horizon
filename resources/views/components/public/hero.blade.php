<section class="relative bg-paper">
    <!-- Background Shape -->
    <div class="absolute inset-0 -z-10">
        <svg width="100%" height="100%" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="opacity-20" fill-opacity="0.05"
                  d="M0,160L48,173.3C96,187,192,213,288,234.7C384,256,480,267,576,266.7C672,266,768,256,864,234.7C960,213,1056,180,1152,173.3C1248,167,1344,187,1440,202.7L1440,320L0,320Z"></path>
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 lg:py-28">
        <div class="text-center">
            <p class="text-sm font-medium text-primary uppercase tracking-wider mb-4">
                {{ $tagline ?? 'Lombok Horizon' }}
            </p>
            <h1 class="text-4xl font-bold text-ink mb-6 sm:text-5xl">
                {{ $title ?? 'Properti Premium di Lombok' }}
            </h1>
            <p class="text-xl text-muted-foreground mb-8 sm:text-2xl max-w-2xl mx-auto">
                {{ $subtitle ?? 'Nikmati keindahan alam dengan investasi yang menguntungkan' }}
            </p>

            @if(!empty($buttonText))
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                @if(!empty($buttonUrl) && $buttonUrl !== '#')
                <a href="{{ $buttonUrl }}"
                   class="flex-1 sm:flex-none px-6 py-4 bg-primary text-white rounded-lg font-medium text-lg hover:bg-primary/90 transition-colors"
                >
                    {{ $buttonText }}
                </a>
                @else
                <button type="button"
                   {{ $attributes->only(['x-on:click', '@click']) }}
                   class="flex-1 sm:flex-none px-6 py-4 bg-primary text-white rounded-lg font-medium text-lg hover:bg-primary/90 transition-colors"
                >
                    {{ $buttonText }}
                </button>
                @endif

                @if(!empty($secondaryButtonText) && !empty($secondaryButtonUrl))
                <a href="{{ $secondaryButtonUrl }}"
                   class="flex-1 sm:flex-none px-6 py-4 bg-white text-primary border border-primary rounded-lg font-medium text-lg hover:bg-primary/50 transition-colors"
                >
                    {{ $secondaryButtonText }}
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>