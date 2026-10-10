<div {{ $attributes->merge(['class' => 'flex items-center justify-between']) }}>
    {{-- Info Text --}}
    <p class="text-[12px] leading-[1.5] text-[#66758c]">
        {{ $infoTextValue }}
    </p>

    {{-- Page Navigation --}}
    @if ($totalPages > 1)
        <nav class="flex items-center gap-1" aria-label="Pagination">
            {{-- Previous --}}
            <button
                type="button"
                class="inline-flex items-center justify-center h-8 w-8 rounded-[6px] text-[#66758c] transition-colors duration-150 {{ $currentPage <= 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#f3f6fa]' }}"
                @disabled($currentPage <= 1)
            >
                <x-heroicon-m-chevron-left class="h-4 w-4" />
            </button>

            {{-- First page + ellipsis --}}
            @if ($pages[0] > 1)
                <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-[6px] text-[12px] font-medium text-[#66758c] hover:bg-[#f3f6fa] transition-colors duration-150">
                    1
                </button>
                @if ($pages[0] > 2)
                    <span class="inline-flex items-center justify-center h-8 w-8 text-[12px] text-[#96a2b4]">...</span>
                @endif
            @endif

            {{-- Page Numbers --}}
            @foreach ($pages as $page)
                <button
                    type="button"
                    class="inline-flex items-center justify-center h-8 w-8 rounded-[6px] text-[12px] font-medium transition-colors duration-150 {{ $page === $currentPage ? 'bg-[#15803d] text-white' : 'text-[#66758c] hover:bg-[#f3f6fa]' }}"
                >
                    {{ $page }}
                </button>
            @endforeach

            {{-- Last page + ellipsis --}}
            @if (end($pages) < $totalPages)
                @if (end($pages) < $totalPages - 1)
                    <span class="inline-flex items-center justify-center h-8 w-8 text-[12px] text-[#96a2b4]">...</span>
                @endif
                <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-[6px] text-[12px] font-medium text-[#66758c] hover:bg-[#f3f6fa] transition-colors duration-150">
                    {{ $totalPages }}
                </button>
            @endif

            {{-- Next --}}
            <button
                type="button"
                class="inline-flex items-center justify-center h-8 w-8 rounded-[6px] text-[#66758c] transition-colors duration-150 {{ $currentPage >= $totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#f3f6fa]' }}"
                @disabled($currentPage >= $totalPages)
            >
                <x-heroicon-m-chevron-right class="h-4 w-4" />
            </button>
        </nav>
    @endif
</div>
