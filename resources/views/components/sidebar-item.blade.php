@php
    $isActive = $isActiveState;
    $activeClasses = $isActive
        ? 'bg-[#ecfdf3] text-[#15803d] font-medium'
        : 'text-[#1a2233] hover:bg-[#f3f6fa] font-normal';
    $iconColor = $isActive ? 'text-[#15803d]' : 'text-[#66758c]';
@endphp

<a
    href="{{ $route !== '#' ? route($route) : '#' }}"
    {{ $attributes->merge(['class' => "flex items-center gap-[10px] w-full p-3 rounded-md text-[13px] leading-[1.45] transition-colors duration-150 {$activeClasses}"]) }}
>
    @if ($icon)
        <span class="shrink-0 {{ $iconColor }}">
            {{ $icon }}
        </span>
    @endif
    <span>{{ $label }}</span>
</a>
