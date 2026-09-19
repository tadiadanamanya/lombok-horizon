@props(['href', 'isActive' => false])
<a href="{{ $href }}"
   @if($isActive) aria-current="page" @endif
   {{ $attributes->merge(['class' => 'px-3 py-2 text-sm font-medium ' . ($isActive ? 'text-primary border-b-2 border-primary' : 'text-muted-foreground hover:text-ink transition-colors')]) }}>
    {{ $slot }}
</a>
