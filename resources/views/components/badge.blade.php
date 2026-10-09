<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full {$component->variantClasses()} {$component->sizeClasses()}"]) }}>
    {{ $slot }}
</span>
