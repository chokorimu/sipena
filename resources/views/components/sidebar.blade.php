<aside {{ $attributes->merge(['class' => 'flex flex-col bg-white w-64 shrink-0 rounded-[12px] border border-[#dfe6ef] shadow-sm sticky top-4 max-h-[calc(100vh-2rem)]']) }}>
    {{-- Logo & App Name --}}
    <div class="flex items-center gap-2 px-5 py-5 border-b border-[#dfe6ef]">
        <div class="flex items-center justify-center h-10 w-10 rounded-md shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="">
        </div>
        <div class="flex flex-col overflow-hidden">
            <span class="text-[11px] leading-[1.6] text-[#66758c]">MTs Negeri Ambon</span>
            <span class="text-[13px] font-bold leading-[1.45] text-[#1a2233]">SiPena</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 flex flex-col gap-1 px-3 py-4 overflow-y-auto">
        {{-- Menu Group Label --}}
        @if (isset($groupLabel))
            <p class="px-3 pb-2 text-[10px] font-semibold text-[#96a2b4] uppercase tracking-wider">
                {{ $groupLabel }}
            </p>
        @endif

        {{ $slot }}
    </nav>

    {{-- Tahun Ajaran --}}
    @if (isset($tahunAjaran))
        <div class="mx-3 mb-3 px-3 py-2.5 bg-[#f3f6fa] rounded-[8px]">
            {{ $tahunAjaran }}
        </div>
    @endif

    {{-- User Profile --}}
    @if (isset($footer))
        <div class="border-t border-[#dfe6ef] px-4 py-4 rounded-b-[12px]">
            {{ $footer }}
        </div>
    @endif
</aside>
