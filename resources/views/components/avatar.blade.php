@if ($src)
    <img
        src="{{ $src }}"
        alt="{{ $name }}"
        {{ $attributes->merge(['class' => "inline-block rounded-full object-cover {$component->sizeClasses()}"]) }}
    />
@else
    <span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full bg-primary-100 text-primary-700 font-semibold {$component->sizeClasses()}"]) }}>
        {{ $component->initials() }}
    </span>
@endif
