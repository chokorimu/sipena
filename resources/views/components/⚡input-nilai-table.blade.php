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
    #[Url(as: 'tahun')]
    public string $tahunId = '';

    #[Url(as: 'kelas')]
    public string $kelasId = '';

    #[Url(as: 'mapel')]
    public string $mapelId = '';

    /**
     * Nilai per siswa: [siswa_id => ['formatif' => ..., 'sumatif' => ...]].
     *
     * @var array<int, array<string, string|null>>
     */
    public array $nilai = [];

    public ?string $pesan = null;

    public string $pesanTipe = 'success';

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

        $this->loadNilai();
    }

    /**
     * Muat ulang daftar siswa & nilai tersimpan ketika filter berubah.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['tahunId', 'kelasId', 'mapelId'], true)) {
            $this->reset('nilai');
            $this->loadNilai();
            $this->pesan = null;
        }
    }

    public function simpan(): void
    {
        $this->validate(
            [
                'nilai.*.formatif' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'nilai.*.sumatif' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ],
            [
                'nilai.*.formatif.numeric' => 'Nilai formatif harus berupa angka.',
                'nilai.*.formatif.max' => 'Nilai formatif maksimal 100.',
                'nilai.*.sumatif.numeric' => 'Nilai sumatif harus berupa angka.',
                'nilai.*.sumatif.max' => 'Nilai sumatif maksimal 100.',
            ]
        );

        $jumlah = 0;

        foreach ($this->siswaList as $siswa) {
            $input = $this->nilai[$siswa->id] ?? null;

            if ($input === null || (blank($input['formatif'] ?? null) && blank($input['sumatif'] ?? null))) {
                continue;
            }

            Nilai::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'mapel_id' => $this->mapelId,
                    'tahun_id' => $this->tahunId,
                ],
                [
                    'formatif' => $input['formatif'],
                    'sumatif' => $input['sumatif'],
                ]
            );

            $jumlah++;
        }

        $this->loadNilai();

        if ($jumlah > 0) {
            $this->pesan = "Nilai berhasil disimpan untuk {$jumlah} siswa.";
            $this->pesanTipe = 'success';
        } else {
            $this->pesan = 'Tidak ada nilai yang disimpan. Isi minimal satu kolom nilai.';
            $this->pesanTipe = 'info';
        }
    }

    public function resetForm(): void
    {
        $this->reset('nilai');
        $this->loadNilai();
        $this->pesan = null;
    }

    /**
     * Muat daftar siswa kelas terpilih beserta nilai yang sudah tersimpan.
     */
    public function loadNilai(): void
    {
        $siswaIds = $this->siswaList->pluck('id');

        $nilaiTersimpan = Nilai::query()
            ->where('mapel_id', $this->mapelId)
            ->where('tahun_id', $this->tahunId)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->keyBy('siswa_id');

        $this->nilai = [];

        foreach ($this->siswaList as $siswa) {
            $this->nilai[$siswa->id] = [
                'formatif' => $nilaiTersimpan->get($siswa->id)?->formatif,
                'sumatif' => $nilaiTersimpan->get($siswa->id)?->sumatif,
            ];
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
    public function siswaList()
    {
        if ($this->kelasId === '' || $this->tahunId === '') {
            return collect();
        }

        return Siswa::query()
            ->whereHas('kelas', function ($query): void {
                $query->where('kelas.id', $this->kelasId)
                    ->where('kelas_siswa.tahun_id', $this->tahunId);
            })
            ->orderBy('nama')
            ->get();
    }

    #[Computed]
    public function nilaiTersimpan()
    {
        return Nilai::query()
            ->where('mapel_id', $this->mapelId)
            ->where('tahun_id', $this->tahunId)
            ->whereIn('siswa_id', $this->siswaList->pluck('id'))
            ->get()
            ->keyBy('siswa_id');
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
};
?>

<div class="flex flex-col gap-6">
    {{-- Filter Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
        <div class="flex-1 w-full sm:w-auto">
            <x-select name="kelas_filter" label="Kelas" wire:model.live="kelasId">
                <option value="">Pilih Kelas</option>
                @foreach ($this->daftarKelas as $kelasItem)
                    <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama_kelas }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex-1 w-full sm:w-auto">
            <x-select name="mapel_filter" label="Mata Pelajaran" wire:model.live="mapelId">
                <option value="">Pilih Mata Pelajaran</option>
                @foreach ($this->daftarMapel as $mapelItem)
                    <option value="{{ $mapelItem->id }}">{{ $mapelItem->nama_mapel }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex-1 w-full sm:w-auto">
            <x-select name="tahun_filter" label="Tahun Ajaran" wire:model.live="tahunId">
                <option value="">Pilih Tahun Ajaran</option>
                @foreach ($this->daftarTahun as $tahunItem)
                    <option value="{{ $tahunItem->id }}">{{ $tahunItem->tahun_ajaran }} &mdash; Semester {{ ucfirst($tahunItem->semester) }}</option>
                @endforeach
            </x-select>
        </div>
        <x-button variant="primary" size="md" wire:click="loadNilai">
            Tampilkan
        </x-button>
    </div>

    {{-- Pesan feedback --}}
    @if ($pesan !== null)
        <div class="flex items-center gap-3 px-4 py-3 rounded-[8px] border {{ $pesanTipe === 'success' ? 'bg-[#ecfdf3] border-[#a7f3d0]' : 'bg-[#f0f4ff] border-[#d0d9f0]' }}">
            <x-heroicon-m-{{ $pesanTipe === 'success' ? 'check-circle' : 'information-circle' }} class="h-5 w-5 shrink-0 {{ $pesanTipe === 'success' ? 'text-[#15803d]' : 'text-[#3b5998]' }}" />
            <p class="text-[13px] leading-[1.45] font-medium {{ $pesanTipe === 'success' ? 'text-[#15803d]' : 'text-[#3b5998]' }}">
                {{ $pesan }}
            </p>
        </div>
    @endif

    {{-- Info Alert --}}
    @if ($this->kelasTerpilih && $this->mapelTerpilih && $this->tahunTerpilih)
        <div class="flex items-center gap-3 px-4 py-3 rounded-[8px] bg-[#f0f4ff] border border-[#d0d9f0]">
            <x-heroicon-m-information-circle class="h-5 w-5 text-[#3b5998] shrink-0" />
            <p class="text-[13px] leading-[1.45] text-[#3b5998] font-medium">
                {{ $this->kelasTerpilih->nama_kelas }} &bull; {{ $this->mapelTerpilih->nama_mapel }} &bull; {{ $this->tahunTerpilih->tahun_ajaran }} Semester {{ ucfirst($this->tahunTerpilih->semester) }}
            </p>
        </div>
    @endif

    {{-- Input Nilai Table --}}
    <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
        <div class="overflow-x-auto">
            <table class="w-full text-[13px] leading-[1.45]">
                <thead>
                    <tr class="border-b border-[#dfe6ef] bg-[#f8f9fb]">
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 60px">No</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 110px">NIS</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 110px">Formatif</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 110px">Sumatif</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 110px">Nilai Akhir</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 120px">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dfe6ef]">
                    @forelse ($this->siswaList as $index => $siswa)
                        @php
                            $nilaiSiswa = $this->nilaiTersimpan->get($siswa->id);
                        @endphp
                        <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }}">
                            <td class="px-5 py-3 text-[#1a2233]">{{ $index + 1 }}</td>
                            <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $siswa->nama }}</td>
                            <td class="px-5 py-3 text-[#66758c]">{{ $siswa->nisn }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <input
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    wire:model="nilai.{{ $siswa->id }}.formatif"
                                    placeholder="-"
                                    class="w-full h-[34px] px-2 text-center text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] bg-white border border-[#dfe6ef] rounded-[6px] focus:outline-none focus:border-[#15803d] focus:ring-1 focus:ring-[#15803d]/20"
                                />
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <input
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    wire:model="nilai.{{ $siswa->id }}.sumatif"
                                    placeholder="-"
                                    class="w-full h-[34px] px-2 text-center text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] bg-white border border-[#dfe6ef] rounded-[6px] focus:outline-none focus:border-[#15803d] focus:ring-1 focus:ring-[#15803d]/20"
                                />
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if ($nilaiSiswa?->nilai_akhir !== null)
                                    <span class="text-[13px] font-semibold text-[#1a2233]">{{ number_format((float) $nilaiSiswa->nilai_akhir, 1) }}</span>
                                @else
                                    <span class="text-[13px] text-[#96a2b4]">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if ($nilaiSiswa?->status_kelulusan === 'Lulus')
                                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#ecfdf3] text-[#15803d]">Lulus</span>
                                @elseif ($nilaiSiswa?->status_kelulusan === 'Tidak Lulus')
                                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#fff0f0] text-[#c13e45]">Tidak Lulus</span>
                                @else
                                    <span class="text-[13px] text-[#96a2b4]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-[13px] text-[#96a2b4]">
                                Tidak ada siswa di kelas ini untuk tahun ajaran terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="flex items-center gap-3">
        <x-button variant="primary" size="md" wire:click="simpan" wire:loading.attr="disabled" wire:target="simpan">
            <x-heroicon-m-check class="h-4 w-4 -ml-0.5" />
            Simpan Nilai
        </x-button>
        <x-button variant="secondary" size="md" wire:click="resetForm">
            Reset
        </x-button>
    </div>
</div>
