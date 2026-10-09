<aside {{ $attributes->merge(['class' => 'flex flex-col bg-white w-64 min-h-screen border-r border-border-light']) }}>
    {{-- Logo & App Name --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-border-light">
        <div class="flex items-center justify-center h-9 w-9 rounded-md bg-primary">
            <span class="text-white font-bold text-body">SP</span>
        </div>
        <div>
            <h2 class="font-h3 text-primary leading-none">SIPENA</h2>
            <p class="text-small text-text-secondary">Sistem Penilaian</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 flex flex-col gap-1 px-3 py-4">
        {{ $slot }}
    </nav>

    {{-- User Profile --}}
    @if (isset($footer))
        <div class="border-t border-border-light px-4 py-4">
            {{ $footer }}
        </div>
    @endif
</aside>
