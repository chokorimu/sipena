<?php

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Url(as: 'tahun')]
    public string $tahunId = '';

    #[Url(as: 'kelas')]
    public string $kelasId = '';

    #[Url(as: 'mapel')]
    public string $mapelId = '';

    public function mount(): void
    {
        if ($this->tahunId === '') {
            $this->tahunId = (string) TahunAjaran::query()->orderByDesc('id')->value('id');
        }

        if ($this->kelasId === '') {
            $this->kelasId = (string) Kelas::query()->orderBy('nama_kelas')->value('id');
        }

        if ($this->mapelId === '') {
            $this->mapelId = (string) MataPelajaran::query()->orderBy('nama_mapel')->value('id');
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['tahunId', 'kelasId', 'mapelId'], true)) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function daftarKelas()
    {
        return Kelas::query()->orderBy('nama_kelas')->get();
    }

    #[Computed]
    public function daftarMapel()
    {
        return MataPelajaran::query()->orderBy('nama_mapel')->get();
    }

    #[Computed]
    public function daftarTahun()
    {
        return TahunAjaran::query()->orderByDesc('id')->get();
    }

    #[Computed]
    public function kelasTerpilih(): ?Kelas
    {
        return Kelas::query()->find($this->kelasId);
    }

    #[Computed]
    public function mapelTerpilih(): ?MataPelajaran
    {
        return MataPelajaran::query()->find($this->mapelId);
    }

    #[Computed]
    public function tahunTerpilih(): ?TahunAjaran
    {
        return TahunAjaran::query()->find($this->tahunId);
    }

    #[Computed]
    public function siswaList()
    {
        return Siswa::query()
            ->whereHas('kelas', function ($query): void {
                $query->where('kelas.id', $this->kelasId)
                    ->where('kelas_siswa.tahun_id', $this->tahunId);
            })
            ->orderBy('nama')
            ->paginate(10);
    }

    #[Computed]
    public function semuaSiswaIds()
    {
        return Siswa::query()
            ->whereHas('kelas', function ($query): void {
                $query->where('kelas.id', $this->kelasId)
                    ->where('kelas_siswa.tahun_id', $this->tahunId);
            })
            ->pluck('id');
    }

    #[Computed]
    public function nilaiRekap()
    {
        return Nilai::query()
            ->where('mapel_id', $this->mapelId)
            ->where('tahun_id', $this->tahunId)
            ->whereIn('siswa_id', $this->semuaSiswaIds)
            ->get()
            ->keyBy('siswa_id');
    }

    /**
     * Statistik kelas dihitung dari SELURUH siswa (bukan hanya halaman aktif).
     *
     * @return array{rata: float|null, tertinggi: float|null, terendah: float|null}
     */
    #[Computed]
    public function statistik(): array
    {
        $nilaiAkhir = $this->nilaiRekap->pluck('nilai_akhir')->filter()->map(fn ($nilai): float => (float) $nilai);

        return [
            'rata' => $nilaiAkhir->isNotEmpty() ? round($nilaiAkhir->avg(), 1) : null,
            'tertinggi' => $nilaiAkhir->isNotEmpty() ? round($nilaiAkhir->max(), 1) : null,
            'terendah' => $nilaiAkhir->isNotEmpty() ? round($nilaiAkhir->min(), 1) : null,
        ];
    }

    /**
     * Peringkat siswa di kelas untuk mapel & tahun terpilih.
     *
     * @return array<int, int>  map siswa_id => peringkat
     */
    #[Computed]
    public function rankings(): array
    {
        return Nilai::hitungRanking($this->nilaiRekap);
    }
};
?>

<div class="flex flex-col gap-6">
    {{-- Filter Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
        <div class="flex-1 w-full sm:w-auto">
            <x-select name="kelas_rekap" label="Kelas" wire:model.live="kelasId">
                <option value="">Pilih Kelas</option>
                @foreach ($this->daftarKelas as $kelasItem)
                    <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama_kelas }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex-1 w-full sm:w-auto">
            <x-select name="tahun_rekap" label="Tahun Ajaran" wire:model.live="tahunId">
                <option value="">Pilih Tahun Ajaran</option>
                @foreach ($this->daftarTahun as $tahunItem)
                    <option value="{{ $tahunItem->id }}">{{ $tahunItem->tahun_ajaran }} &mdash; Semester {{ ucfirst($tahunItem->semester) }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex-1 w-full sm:w-auto">
            <x-select name="mapel_rekap" label="Mata Pelajaran" wire:model.live="mapelId">
                <option value="">Pilih Mata Pelajaran</option>
                @foreach ($this->daftarMapel as $mapelItem)
                    <option value="{{ $mapelItem->id }}">{{ $mapelItem->nama_mapel }}</option>
                @endforeach
            </x-select>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Rata-rata Kelas --}}
        <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#f0f4ff] text-[#3b5998] shrink-0">
                <x-heroicon-m-calculator class="h-5 w-5" />
            </div>
            <div class="flex flex-col gap-0.5">
                <p class="text-[13px] leading-[1.45] text-[#66758c]">Rata-rata Kelas</p>
                <p class="text-[24px] font-bold leading-[1.3] text-[#1a2233]">
                    {{ $this->statistik['rata'] !== null ? number_format($this->statistik['rata'], 1) : '-' }}
                </p>
            </div>
        </div>

        {{-- Nilai Tertinggi --}}
        <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#ecfdf3] text-[#15803d] shrink-0">
                <x-heroicon-m-arrow-trending-up class="h-5 w-5" />
            </div>
            <div class="flex flex-col gap-0.5">
                <p class="text-[13px] leading-[1.45] text-[#66758c]">Nilai Tertinggi</p>
                <p class="text-[24px] font-bold leading-[1.3] text-[#15803d]">
                    {{ $this->statistik['tertinggi'] !== null ? number_format($this->statistik['tertinggi'], 1) : '-' }}
                </p>
            </div>
        </div>

        {{-- Nilai Terendah --}}
        <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#fff0f0] text-[#c13e45] shrink-0">
                <x-heroicon-m-arrow-trending-down class="h-5 w-5" />
            </div>
            <div class="flex flex-col gap-0.5">
                <p class="text-[13px] leading-[1.45] text-[#66758c]">Nilai Terendah</p>
                <p class="text-[24px] font-bold leading-[1.3] text-[#c13e45]">
                    {{ $this->statistik['terendah'] !== null ? number_format($this->statistik['terendah'], 1) : '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Rekap Table --}}
    <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
        <div class="overflow-x-auto">
            <table class="w-full text-[13px] leading-[1.45]">
                <thead>
                    <tr class="border-b border-[#dfe6ef] bg-[#f8f9fb]">
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 60px">No</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">Peringkat</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 110px">NIS</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 90px">Formatif</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 90px">Sumatif</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">Nilai Akhir</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 90px">Predikat</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 120px">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dfe6ef]">
                    @forelse ($this->siswaList as $index => $siswa)
                        @php
                            $nilai = $this->nilaiRekap->get($siswa->id);
                            $predikat = $nilai?->predikat();
                            $peringkat = $this->rankings[$siswa->id] ?? null;
                            $peringkatColors = match ($peringkat) {
                                1 => 'bg-[#fff5df] text-[#a56708]',
                                2 => 'bg-[#f3f6fa] text-[#66758c]',
                                3 => 'bg-[#fff0f0] text-[#c13e45]',
                                default => '',
                            };
                            $predikatColors = match ($predikat) {
                                'A' => 'bg-[#ecfdf3] text-[#15803d]',
                                'B' => 'bg-[#ecfdf3] text-[#15803d]',
                                'C' => 'bg-[#f0f4ff] text-[#3b5998]',
                                'D' => 'bg-[#fff5df] text-[#a56708]',
                                default => 'bg-[#fff0f0] text-[#c13e45]',
                            };
                            $statusColors = match ($nilai?->status_kelulusan) {
                                'Lulus' => 'bg-[#ecfdf3] text-[#15803d]',
                                'Tidak Lulus' => 'bg-[#fff0f0] text-[#c13e45]',
                                default => '',
                            };
                        @endphp
                        <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }} hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                            <td class="px-5 py-3 text-[#1a2233]">{{ $this->siswaList->firstItem() + $index }}</td>
                            <td class="px-5 py-3 text-center">
                                @if ($peringkat !== null && $peringkat <= 3)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-full {{ $peringkatColors }}">
                                        <x-heroicon-m-trophy class="h-3 w-3" />
                                        {{ $peringkat }}
                                    </span>
                                @elseif ($peringkat !== null)
                                    <span class="text-[13px] font-medium text-[#1a2233]">{{ $peringkat }}</span>
                                @else
                                    <span class="text-[13px] text-[#96a2b4]">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $siswa->nama }}</td>
                            <td class="px-5 py-3 text-[#66758c]">{{ $siswa->nisn }}</td>
                            <td class="px-5 py-3 text-center text-[#1a2233]">{{ $nilai?->formatif !== null ? number_format((float) $nilai->formatif, 1) : '-' }}</td>
                            <td class="px-5 py-3 text-center text-[#1a2233]">{{ $nilai?->sumatif !== null ? number_format((float) $nilai->sumatif, 1) : '-' }}</td>
                            <td class="px-5 py-3 text-center">
                                @if ($nilai?->nilai_akhir !== null)
                                    <span class="font-semibold text-[#1a2233]">{{ number_format((float) $nilai->nilai_akhir, 1) }}</span>
                                @else
                                    <span class="text-[13px] text-[#96a2b4]">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if ($predikat)
                                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full {{ $predikatColors }}">{{ $predikat }}</span>
                                @else
                                    <span class="text-[13px] text-[#96a2b4]">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if ($nilai?->status_kelulusan)
                                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full {{ $statusColors }}">{{ $nilai->status_kelulusan }}</span>
                                @else
                                    <span class="text-[13px] text-[#96a2b4]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-[13px] text-[#96a2b4]">
                                Tidak ada siswa di kelas ini untuk tahun ajaran terpilih.
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
