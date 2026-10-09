<header {{ $attributes->merge(['class' => 'flex items-center justify-between bg-white px-6 py-4 border-b border-border-light']) }}>
    <div class="flex items-center gap-4">
        @if ($title)
            <h1 class="font-h2 text-text-main">{{ $title }}</h1>
        @endif
        {{ $slot }}
    </div>

    <div class="flex items-center gap-4">
        @if (isset($actions))
            {{ $actions }}
        @endif
    </div>
</header>
