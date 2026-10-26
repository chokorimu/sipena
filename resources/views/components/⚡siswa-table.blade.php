<?php

use App\Models\Kelas;
use App\Models\Siswa;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'kelas')]
    public string $kelas = '';

    /**
     * Reset halaman ketika filter berubah.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'kelas'], true)) {
            $this->resetPage();
        }
    }

    #[ Computed]
public function siswaList()
{
 return Siswa:: query()
 -> when($this-> search !== '', function ($query): void {
 $query-> where(function ($query): void {
 $query-> where('nama', 'like', '%'.$this-> search.'%')
 -> orWhere('nisn', 'like', '%'.$this-> search.'%');
 });
 })
 -> when($this-> kelas !== '', function ($query): void {
 $query-> whereHas('kelas', function ($query): void {
 $query-> where('kelas.id', $this-> kelas);
 });
 })
 -> with(['kelas' => function ($query): void {
 $query-> orderByPivot('tahun_id', 'desc');
 }])
 -> latest()
 -> paginate(10);
}

#[ Computed]
public function daftarKelas()
{
 return Kelas:: query()-> orderBy('nama_kelas')-> get();
}
}?>

<div class="flex flex-col gap-6">
    {{-- Filters & Search --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="flex-1 w-full sm:w-auto">
            <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIS siswa..." />
        </div>
        <div class="flex items-center gap-3">
            <x-select name="kelas" wire:model.live="kelas" class="w-[180px]">
                <option value="">Semua Kelas</option>
                @foreach ($this->daftarKelas as $kelasItem)
                    <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama_kelas }}</option>
                @endforeach
            </x-select>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
        <div class="overflow-x-auto">
            <table class="w-full text-[13px] leading-[1.45]">
                <thead>
                    <tr class="border-b border-[#dfe6ef] bg-[#f8f9fb]">
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 60px">No</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">NIS</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Kelas</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dfe6ef]">
                    @forelse ($this->siswaList as $index => $siswa)
                        <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }} hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                            <td class="px-5 py-3 text-[#1a2233]">{{ $this->siswaList->firstItem() + $index }}</td>
                            <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $siswa->nama }}</td>
                            <td class="px-5 py-3 text-[#66758c]">{{ $siswa->nisn }}</td>
                            <td class="px-5 py-3 text-[#66758c]">{{ $siswa->kelas->first()?->nama_kelas ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-[#66758c] hover:text-[#15803d] hover:bg-[#ecfdf3] transition-colors duration-150" title="Edit">
                                        <x-heroicon-o-pencil-square class="h-4 w-4" />
                                    </button>
                                    <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-[#66758c] hover:text-[#c13e45] hover:bg-[#fff0f0] transition-colors duration-150" title="Hapus">
                                        <x-heroicon-o-trash class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-[13px] text-[#96a2b4]">
                                Tidak ada data siswa yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @php
            $currentPage = $this->siswaList->currentPage();
            $lastPage = $this->siswaList->lastPage();
            $pages = collect(range(max(1, $currentPage - 1), min($lastPage, $currentPage + 1)));
        @endphp
        <div class="flex items-center justify-between px-5 py-3 border-t border-[#dfe6ef] bg-[#f8f9fb] rounded-b-[12px]">
            <p class="text-[13px] leading-[1.45] text-[#66758c]">
                Menampilkan <span class="font-medium text-[#1a2233]">{{ $this->siswaList->firstItem() ?? 0 }}-{{ $this->siswaList->lastItem() ?? 0 }}</span>
                dari <span class="font-medium text-[#1a2233]">{{ $this->siswaList->total() }}</span> data
            </p>
            @if ($lastPage > 1)
                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        wire:click="previousPage"
                        @disabled($this->siswaList->onFirstPage())
                        class="inline-flex items-center justify-center h-8 w-8 rounded-[6px] text-[#66758c] transition-colors duration-150 {{ $this->siswaList->onFirstPage() ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#f3f6fa]' }}"
                    >
                        <x-heroicon-m-chevron-left class="h-4 w-4" />
                    </button>

                    @if ($pages->first() > 1)
                        <button type="button" wire:click="gotoPage(1)" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-[6px] text-[12px] font-medium text-[#66758c] hover:bg-[#f3f6fa] transition-colors duration-150">1</button>
                        @if ($pages->first() > 2)
                            <span class="inline-flex items-center justify-center h-8 w-8 text-[12px] text-[#96a2b4]">...</span>
                        @endif
                    @endif

                    @foreach ($pages as $page)
                        <button
                            type="button"
                            wire:click="gotoPage({{ $page }})"
                            class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-[6px] text-[12px] font-medium transition-colors duration-150 {{ $page === $currentPage ? 'bg-[#15803d] text-white' : 'text-[#66758c] hover:bg-[#f3f6fa]' }}"
                        >
                            {{ $page }}
                        </button>
                    @endforeach

                    @if ($pages->last() < $lastPage)
                        @if ($pages->last() < $lastPage - 1)
                            <span class="inline-flex items-center justify-center h-8 w-8 text-[12px] text-[#96a2b4]">...</span>
                        @endif
                        <button type="button" wire:click="gotoPage({{ $lastPage }})" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-[6px] text-[12px] font-medium text-[#66758c] hover:bg-[#f3f6fa] transition-colors duration-150">{{ $lastPage }}</button>
                    @endif

                    <button
                        type="button"
                        wire:click="nextPage"
                        @disabled($currentPage >= $lastPage)
                        class="inline-flex items-center justify-center h-8 w-8 rounded-[6px] text-[#66758c] transition-colors duration-150 {{ $currentPage >= $lastPage ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#f3f6fa]' }}"
                    >
                        <x-heroicon-m-chevron-right class="h-4 w-4" />
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
