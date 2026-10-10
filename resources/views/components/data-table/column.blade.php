@php
    $widthStyle = $width ? "width: {$width};" : '';
@endphp

<th
    {{ $attributes->merge(['class' => "px-5 py-3 font-semibold text-[11px] leading-[1.6] text-[#66758c] uppercase tracking-wider {$alignClass}"]) }}
    @if ($widthStyle) style="{{ $widthStyle }}" @endif
>
    <div class="flex items-center gap-1 {{ $align === 'right' ? 'justify-end' : ($align === 'center' ? 'justify-center' : '') }}">
        <span>{{ $label }}</span>
        @if ($sortable)
            <span class="text-[#96a2b4] shrink-0">
                <x-heroicon-m-chevron-up-down class="h-3.5 w-3.5" />
            </span>
        @endif
    </div>
</th>
