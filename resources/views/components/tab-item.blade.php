@php
    $baseClasses = 'inline-flex items-center gap-1.5 px-4 py-2.5 text-[13px] leading-[1.45] font-medium border-b-2 transition-colors duration-150 whitespace-nowrap';
    $activeClasses = $active
        ? 'border-[#15803d] text-[#15803d]'
        : 'border-transparent text-[#66758c] hover:text-[#1a2233] hover:border-[#dfe6ef]';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "{$baseClasses} {$activeClasses}"]) }} role="tab" @if ($active) aria-selected="true" @endif>
        {{ $slot->isEmpty() ? $label : $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => "{$baseClasses} {$activeClasses}"]) }} role="tab" @if ($active) aria-selected="true" @endif>
        {{ $slot->isEmpty() ? $label : $slot }}
    </button>
@endif
