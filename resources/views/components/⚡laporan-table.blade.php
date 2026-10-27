<?php

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    #[Url(as: 'kelas')]
    public string $kelasId = '';

    #[Url(as: 'tahun')]
    public string $tahunId = '';

    #[Url(as: 'jenis')]
    public string $jenis = 'rekap-kelas';

    public function mount(): void
    {
        if ($this->kelasId === '') {
            $this->kelasId = (string) Kelas::query()->orderBy('nama_kelas')->value('id');
        }

        if ($this->tahunId === '') {
            $this->tahunId = (string) TahunAjaran::query()->orderByDesc('id')->value('id');
        }
    }

    #[Computed]
    public function daftarKelas()
    {
        return Kelas::query()->orderBy('nama_kelas')->get();
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
    public function tahunTerpilih(): ?TahunAjaran
    {
        return TahunAjaran::query()->find($this->tahunId);
    }

    #[Computed]
    public function siswaIds()
    {
        return Siswa::query()
            ->whereHas('kelas', function ($query): void {
                $query->where('kelas.id', $this->kelasId)
                    ->where('kelas_siswa.tahun_id', $this->tahunId);
            })
            ->pluck('id');
    }

    /**
     * Seluruh nilai siswa di kelas & tahun terpilih.
     */
    #[Computed]
    public function nilaiKelas()
    {
        return Nilai::query()
            ->with('mataPelajaran')
            ->where('tahun_id', $this->tahunId)
            ->whereIn('siswa_id', $this->siswaIds)
            ->get()
            ->filter(fn (Nilai $nilai): bool => $nilai->nilai_akhir !== null);
    }

    /**
     * @return array{totalSiswa: int, tuntas: int, belumTuntas: int, rataRata: float|null}
     */
    #[Computed]
    public function statistik(): array
    {
        $rataPerSiswa = $this->nilaiKelas
            ->groupBy('siswa_id')
            ->map(fn ($nilaiSiswa): float => round((float) $nilaiSiswa->avg('nilai_akhir'), 2));

        $semuaNilai = $this->nilaiKelas->pluck('nilai_akhir')->map(fn ($nilai): float => (float) $nilai);

        return [
            'totalSiswa' => $this->siswaIds->count(),
            'tuntas' => $rataPerSiswa->filter(fn (float $rata): bool => $rata >= Nilai::KKM)->count(),
            'belumTuntas' => $rataPerSiswa->filter(fn (float $rata): bool => $rata < Nilai::KKM)->count(),
            'rataRata' => $semuaNilai->isNotEmpty() ? round($semuaNilai->avg(), 1) : null,
        ];
    }

    /**
     * Ringkasan nilai per mata pelajaran.
     *
     * @return \Illuminate\Support\Collection<int, array{mapel: string, rata: float, tertinggi: float, terendah: float, tuntas: int, belum: int}>
     */
    #[Computed]
    public function ringkasanMapel()
    {
        return $this->nilaiKelas
            ->groupBy('mapel_id')
            ->map(function ($nilaiMapel): array {
                $nilaiAkhir = $nilaiMapel->pluck('nilai_akhir')->map(fn ($nilai): float => (float) $nilai);

                return [
                    'mapel' => $nilaiMapel->first()->mataPelajaran->nama_mapel,
                    'rata' => round($nilaiAkhir->avg(), 1),
                    'tertinggi' => round($nilaiAkhir->max(), 1),
                    'terendah' => round($nilaiAkhir->min(), 1),
                    'tuntas' => $nilaiAkhir->filter(fn (float $nilai): bool => $nilai >= Nilai::KKM)->count(),
                    'belum' => $nilaiAkhir->filter(fn (float $nilai): bool => $nilai < Nilai::KKM)->count(),
                ];
            })
            ->sortBy('mapel')
            ->values();
    }

    /**
     * Distribusi predikat A-E dari seluruh nilai kelas.
     *
     * @return array<int, array{label: string, jumlah: int, persen: int, variant: string}>
     */
    #[Computed]
    public function distribusiPredikat(): array
    {
        $total = $this->nilaiKelas->count();

        $hitung = $this->nilaiKelas
            ->groupBy(fn (Nilai $nilai): string => $nilai->predikat() ?? '-')
            ->map(fn ($grup): int => $grup->count());

        $variants = ['A' => 'primary', 'B' => 'success', 'C' => 'info', 'D' => 'warning', 'E' => 'danger'];

        return collect(['A', 'B', 'C', 'D', 'E'])
            ->map(function (string $label) use ($hitung, $total, $variants): array {
                $jumlah = $hitung->get($label, 0);

                return [
                    'label' => $label,
                    'jumlah' => $jumlah,
                    'persen' => $total > 0 ? (int) round($jumlah / $total * 100) : 0,
                    'variant' => $variants[$label],
                ];
            })
            ->all();
    }

    /**
     * Catatan & rekomendasi yang di-generate dari data kelas.
     *
     * @return array<int, array{tipe: 'success'|'warning', judul: string, isi: string}>
     */
    #[Computed]
    public function catatan(): array
    {
        $catatan = [];
        $statistik = $this->statistik;
        $ringkasan = $this->ringkasanMapel;

        if ($ringkasan->isEmpty()) {
            return [[
                'tipe' => 'warning',
                'judul' => 'Belum Ada Data',
                'isi' => 'Belum ada nilai yang tercatat untuk kelas dan tahun ajaran terpilih.',
            ]];
        }

        $persenTuntas = $statistik['totalSiswa'] > 0
            ? (int) round($statistik['tuntas'] / $statistik['totalSiswa'] * 100)
            : 0;

        $catatan[] = [
            'tipe' => $persenTuntas >= 75 ? 'success' : 'warning',
            'judul' => $persenTuntas >= 75 ? 'Pencapaian KKM Baik' : 'Pencapaian KKM Perlu Perhatian',
            'isi' => "{$persenTuntas}% siswa ({$statistik['tuntas']} dari {$statistik['totalSiswa']}) mencapai KKM ".Nilai::KKM.' berdasarkan rata-rata seluruh mata pelajaran.',
        ];

        $mapelTerburuk = $ringkasan->sortByDesc('belum')->first();

        if ($mapelTerburuk !== null && $mapelTerburuk['belum'] > 0) {
            $catatan[] = [
                'tipe' => 'warning',
                'judul' => "Perhatian pada {$mapelTerburuk['mapel']}",
                'isi' => "{$mapelTerburuk['belum']} siswa belum tuntas pada mata pelajaran {$mapelTerburuk['mapel']}. Disarankan untuk memberikan jam tambahan atau bimbingan khusus.",
            ];
        }

        $mapelTerbaik = $ringkasan->sortByDesc('rata')->first();

        if ($mapelTerbaik !== null) {
            $catatan[] = [
                'tipe' => 'success',
                'judul' => "{$mapelTerbaik['mapel']} Terbaik",
                'isi' => "Mata pelajaran {$mapelTerbaik['mapel']} memiliki rata-rata tertinggi, {$mapelTerbaik['rata']}, dengan {$mapelTerbaik['tuntas']} siswa tuntas.",
            ];
        }

        if ($statistik['belumTuntas'] > 0) {
            $catatan[] = [
                'tipe' => 'warning',
                'judul' => 'Remedial Diperlukan',
                'isi' => "{$statistik['belumTuntas']} siswa memiliki rata-rata di bawah KKM dan memerlukan program remedial.",
            ];
        }

        return $catatan;
    }
};
?>

<div class="flex flex-col gap-6">
    {{-- Filter Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
        <div class="flex-1 w-full sm:w-auto">
            <x-select name="kelas_laporan" label="Kelas" wire:model.live="kelasId">
                <option value="">Pilih Kelas</option>
                @foreach ($this->daftarKelas as $kelasItem)
                    <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama_kelas }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex-1 w-full sm:w-auto">
            <x-select name="tahun_laporan" label="Tahun Ajaran" wire:model.live="tahunId">
                <option value="">Pilih Tahun Ajaran</option>
                @foreach ($this->daftarTahun as $tahunItem)
                    <option value="{{ $tahunItem->id }}">{{ $tahunItem->tahun_ajaran }} &mdash; Semester {{ ucfirst($tahunItem->semester) }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex-1 w-full sm:w-auto">
            <x-select name="jenis_laporan" label="Jenis Laporan" wire:model.live="jenis">
                <option value="rekap-kelas">Rekap Kelas</option>
                <option value="rapor-siswa">Rapor Siswa</option>
                <option value="analisis-nilai">Analisis Nilai</option>
            </x-select>
        </div>
    </div>

    @if ($jenis !== 'rekap-kelas')
        {{-- Jenis laporan lain belum tersedia --}}
        <div class="flex flex-col items-center gap-3 px-5 py-16 bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-[#f0f4ff] text-[#3b5998]">
                <x-heroicon-o-document-text class="h-6 w-6" />
            </div>
            <p class="text-[14px] font-semibold text-[#1a2233]">
                {{ $jenis === 'rapor-siswa' ? 'Rapor Siswa' : 'Analisis Nilai' }} belum tersedia
            </p>
            <p class="text-[13px] text-[#66758c] text-center max-w-[420px]">
                Jenis laporan ini masih dalam pengembangan. Saat ini hanya <span class="font-medium text-[#1a2233]">Rekap Kelas</span> yang sudah terhubung ke database.
            </p>
        </div>
    @else
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Siswa --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#f0f4ff] text-[#3b5998] shrink-0">
                    <x-heroicon-m-users class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Total Siswa</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#1a2233]">{{ $this->statistik['totalSiswa'] }}</p>
                </div>
            </div>

            {{-- Tuntas --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#ecfdf3] text-[#15803d] shrink-0">
                    <x-heroicon-m-check-circle class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Tuntas</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#15803d]">{{ $this->statistik['tuntas'] }}</p>
                </div>
            </div>

            {{-- Belum Tuntas --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#fff0f0] text-[#c13e45] shrink-0">
                    <x-heroicon-m-x-circle class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Belum Tuntas</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#c13e45]">{{ $this->statistik['belumTuntas'] }}</p>
                </div>
            </div>

            {{-- Rata-rata --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#fff5df] text-[#a56708] shrink-0">
                    <x-heroicon-m-chart-bar class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Rata-rata</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#1a2233]">
                        {{ $this->statistik['rataRata'] !== null ? number_format($this->statistik['rataRata'], 1) : '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Ringkasan Nilai per Mata Pelajaran --}}
        <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
            <div class="px-5 py-4 border-b border-[#dfe6ef]">
                <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">Ringkasan Nilai per Mata Pelajaran</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-[13px] leading-[1.45]">
                    <thead>
                        <tr class="border-b border-[#dfe6ef] bg-[#f8f9fb]">
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 60px">No</th>
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Rata-rata Kelas</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Tertinggi</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Terendah</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Tuntas</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Belum Tuntas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dfe6ef]">
                        @forelse ($this->ringkasanMapel as $index => $mapel)
                            <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }} hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                                <td class="px-5 py-3 text-[#1a2233]">{{ $index + 1 }}</td>
                                <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $mapel['mapel'] }}</td>
                                <td class="px-5 py-3 text-center font-semibold text-[#1a2233]">{{ number_format($mapel['rata'], 1) }}</td>
                                <td class="px-5 py-3 text-center text-[#15803d] font-medium">{{ number_format($mapel['tertinggi'], 1) }}</td>
                                <td class="px-5 py-3 text-center text-[#c13e45] font-medium">{{ number_format($mapel['terendah'], 1) }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#ecfdf3] text-[#15803d]">{{ $mapel['tuntas'] }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($mapel['belum'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#fff0f0] text-[#c13e45]">{{ $mapel['belum'] }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#f3f6fa] text-[#66758c]">0</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-[13px] text-[#96a2b4]">
                                    Belum ada nilai yang tercatat untuk kelas dan tahun ajaran terpilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bottom Row: Distribusi Predikat & Catatan --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Distribusi Predikat --}}
            <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
                <div class="px-5 py-4 border-b border-[#dfe6ef]">
                    <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">Distribusi Predikat</h3>
                </div>
                <div class="p-5 flex flex-col gap-4">
                    @foreach ($this->distribusiPredikat as $item)
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Predikat {{ $item['label'] }}</span>
                                <span class="text-[13px] leading-[1.45] text-[#66758c]">{{ $item['jumlah'] }} nilai</span>
                            </div>
                            <x-progress-bar
                                :value="$item['persen']"
                                :variant="$item['variant']"
                                size="sm"
                            />
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Catatan & Rekomendasi --}}
            <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
                <div class="px-5 py-4 border-b border-[#dfe6ef]">
                    <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">Catatan &amp; Rekomendasi</h3>
                </div>
                <div class="p-5 flex flex-col divide-y divide-[#dfe6ef]">
                    @foreach ($this->catatan as $item)
                        <div class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-[8px] shrink-0 {{ $item['tipe'] === 'success' ? 'bg-[#ecfdf3] text-[#15803d]' : 'bg-[#fff5df] text-[#a56708]' }}">
                                <x-heroicon-m-{{ $item['tipe'] === 'success' ? 'check' : 'exclamation-triangle' }} class="h-4 w-4" />
                            </div>
                            <div class="flex flex-col gap-0.5 min-w-0">
                                <p class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">{{ $item['judul'] }}</p>
                                <p class="text-[12px] leading-[1.6] text-[#66758c]">{{ $item['isi'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
