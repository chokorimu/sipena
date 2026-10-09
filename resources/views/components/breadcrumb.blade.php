<nav {{ $attributes->merge(['class' => 'flex items-center']) }} aria-label="Breadcrumb">
    <ol class="flex items-center gap-1.5 text-caption text-text-secondary">
        {{ $slot }}
    </ol>
</nav>
