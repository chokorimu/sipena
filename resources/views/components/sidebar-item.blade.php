@php
    $isActive = $component->isActive();
    $activeClasses = $isActive
        ? 'bg-primary text-white'
        : 'text-text-main hover:bg-neutral-100';
    $iconColor = $isActive ? 'text-white' : 'text-text-secondary';
@endphp

<a
    href="{{ $route !== '#' ? route($route) : '#' }}"
    {{ $attributes->merge(['class' => "flex items-center gap-3 w-full px-3 py-2.5 rounded-md text-body font-medium transition-colors duration-150 {$activeClasses}"]) }}
>
    @if ($icon)
        <span class="shrink-0 {{ $iconColor }}">
            {{ $icon }}
        </span>
    @endif
    <span>{{ $label }}</span>
</a>
