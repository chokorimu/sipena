<li class="flex items-center gap-1.5">
    @if (!$active && $href)
        <a href="{{ $href }}" class="hover:text-[#15803d] transition-colors duration-150">{{ $label }}</a>
    @else
        <span class="{{ $active ? 'text-[#1a2233] font-medium' : '' }}">{{ $label }}</span>
    @endif

    @if (!$active)
        <x-heroicon-m-chevron-right class="h-3.5 w-3.5 text-[#96a2b4] shrink-0" />
    @endif
</li>
