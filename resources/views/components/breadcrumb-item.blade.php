<li class="flex items-center gap-1.5">
    @if (!$active && $href)
        <a href="{{ $href }}" class="hover:text-primary transition-colors duration-150">{{ $label }}</a>
    @else
        <span class="{{ $active ? 'text-text-main font-medium' : '' }}">{{ $label }}</span>
    @endif

    @if (!$active)
        <x-heroicon-m-chevron-right class="h-3.5 w-3.5 text-neutral-400 shrink-0" />
    @endif
</li>
