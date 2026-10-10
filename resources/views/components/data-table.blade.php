<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full text-[13px] leading-[1.45]']) }}>
        {{-- Table Head --}}
        <thead>
            <tr class="border-b border-[#dfe6ef] bg-[#f8f9fb]">
                {{ $head ?? '' }}
            </tr>
        </thead>

        {{-- Table Body --}}
        <tbody class="divide-y divide-[#dfe6ef]">
            @forelse ($rows as $index => $row)
                <tr class="{{ $striped && $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }} {{ $hoverable ? 'hover:bg-[#ecfdf3]/30 transition-colors duration-100' : '' }}">
                    @if (isset($row_template))
                        {{ $row_template($row, $index) }}
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="100" class="px-5 py-12 text-center">
                        @if (isset($empty))
                            {{ $empty }}
                        @else
                            <div class="flex flex-col items-center gap-2">
                                <x-heroicon-o-inbox class="h-10 w-10 text-[#dfe6ef]" />
                                <p class="text-[#96a2b4] text-[13px]">Tidak ada data ditemukan</p>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer / Pagination --}}
    @if (isset($footer))
        <div class="px-5 py-3 border-t border-[#dfe6ef] bg-[#f8f9fb]">
            {{ $footer }}
        </div>
    @endif
</div>
