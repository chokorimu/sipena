@if ($divider)
    <div class="border-t border-[#dfe6ef] my-1"></div>
@endif

@php
    $baseClasses = 'flex items-center gap-2 w-full p-[10px] text-[12px] leading-[1.5] rounded-[4px] transition-colors duration-100';
    $colorClasses = $danger
        ? 'text-[#c13e45] hover:bg-[#fff0f0]'
        : 'text-[#1a2233] hover:bg-[#f3f6fa]';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "{$baseClasses} {$colorClasses}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => "{$baseClasses} {$colorClasses}"]) }}>
        {{ $slot }}
    </button>
@endif
