@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold transition-colors duration-150 focus:outline-none cursor-pointer';
    $disabledClasses = $disabled ? 'bg-[#e9edf3] border-[#e9edf3] text-[#96a2b4] cursor-not-allowed pointer-events-none' : '';
    $classes = "{$baseClasses} {$sizeClass} " . ($disabled ? $disabledClasses : $variantClass);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @disabled($disabled)>
        {{ $slot }}
    </button>
@endif
