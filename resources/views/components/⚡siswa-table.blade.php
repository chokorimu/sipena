<?php

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Validation\Rule;
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

    // Form modal siswa
    public ?int $siswaId = null;

    public string $nisn = '';

    public string $nama = '';

    public string $jenis_kelamin = 'L';

    public string $tanggal_lahir = '';

    public string $alamat = '';

    public string $formKelasId = '';

    public string $formTahunId = '';

    // Konfirmasi hapus
    public ?int $siswaHapusId = null;

    public string $siswaHapusNama = '';

    // Feedback
    public ?string $pesan = null;

    public string $pesanTipe = 'success';

    /**
     * Reset halaman ketika filter berubah.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'kelas'], true)) {
            $this->resetPage();
        }
    }

    public function tambah(): void
    {
        $this->resetForm();

        $this->formKelasId = $this->kelas !== ''
            ? $this->kelas
            : (string) Kelas::query()->orderBy('nama_kelas')->value('id');
        $this->formTahunId = (string) TahunAjaran::query()->orderByDesc('id')->value('id');

        $this->dispatch('open-modal', id: 'modal-form-siswa');
    }

    public function edit(int $id): void
    {
        $siswa = Siswa::query()->findOrFail($id);

        $this->resetForm();

        $this->siswaId = $siswa->id;
        $this->nisn = $siswa->nisn;
        $this->nama = $siswa->nama;
        $this->jenis_kelamin = $siswa->jenis_kelamin;
        $this->tanggal_lahir = $siswa->tanggal_lahir->format('Y-m-d');
        $this->alamat = (string) $siswa->alamat;

        $kelasTerakhir = $siswa->kelas()->orderByPivot('tahun_id', 'desc')->first();
        $this->formKelasId = (string) ($kelasTerakhir?->id ?? '');
        $this->formTahunId = (string) ($kelasTerakhir?->pivot->tahun_id ?? '');

        $this->dispatch('open-modal', id: 'modal-form-siswa');
    }

    public function simpan(): void
    {
        $this->validate(
            [
                'nisn' => ['required', 'digits:10', Rule::unique('siswa', 'nisn')->ignore($this->siswaId)],
                'nama' => ['required', 'string', 'max:255'],
                'jenis_kelamin' => ['required', 'in:L,P'],
                'tanggal_lahir' => ['required', 'date'],
                'alamat' => ['nullable', 'string'],
                'formKelasId' => ['required', 'exists:kelas,id'],
                'formTahunId' => ['required', 'exists:tahun_ajaran,id'],
            ],
            [
                'nisn.required' => 'NISN wajib diisi.',
                'nisn.digits' => 'NISN harus terdiri dari 10 angka.',
                'nisn.unique' => 'NISN sudah terdaftar.',
                'nama.required' => 'Nama siswa wajib diisi.',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
                'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
                'tanggal_lahir.date' => 'Tanggal lahir tidak valid.',
                'formKelasId.required' => 'Kelas wajib dipilih.',
                'formTahunId.required' => 'Tahun ajaran wajib dipilih.',
            ]
        );

        $data = [
            'nisn' => $this->nisn,
            'nama' => $this->nama,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tanggal_lahir' => $this->tanggal_lahir,
            'alamat' => $this->alamat,
        ];

        if ($this->siswaId !== null) {
            $siswa = Siswa::query()->findOrFail($this->siswaId);
            $siswa->update($data);
            $pesan = 'Data siswa berhasil diperbarui.';
        } else {
            $siswa = Siswa::create($data);
            $pesan = 'Siswa baru berhasil ditambahkan.';
        }

        $siswa->kelas()->syncWithoutDetaching([
            $this->formKelasId => ['tahun_id' => $this->formTahunId],
        ]);

        $this->dispatch('close-modal', id: 'modal-form-siswa');
        $this->resetForm();
        $this->pesan = $pesan;
        $this->pesanTipe = 'success';
    }

    public function konfirmasiHapus(int $id): void
    {
        $siswa = Siswa::query()->findOrFail($id);

        $this->siswaHapusId = $siswa->id;
        $this->siswaHapusNama = $siswa->nama;

        $this->dispatch('open-confirm', id: 'konfirmasi-hapus-siswa');
    }

    public function hapusSekarang(): void
    {
        if ($this->siswaHapusId === null) {
            return;
        }

        $siswa = Siswa::query()->findOrFail($this->siswaHapusId);
        $nama = $siswa->nama;
        $siswa->delete();

        $this->dispatch('close-confirm', id: 'konfirmasi-hapus-siswa');
        $this->reset(['siswaHapusId', 'siswaHapusNama']);
        $this->pesan = "Siswa {$nama} berhasil dihapus.";
        $this->pesanTipe = 'success';
    }

    public function batalHapus(): void
    {
        $this->dispatch('close-confirm', id: 'konfirmasi-hapus-siswa');
        $this->reset(['siswaHapusId', 'siswaHapusNama']);
    }

    public function tutupModal(): void
    {
        $this->dispatch('close-modal', id: 'modal-form-siswa');
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['siswaId', 'nisn', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'formKelasId', 'formTahunId']);
        $this->jenis_kelamin = 'L';
        $this->resetValidation();
    }

    #[Computed]
    public function siswaList()
    {
        return Siswa::query()
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('nama', 'like', '%'.$this->search.'%')
                        ->orWhere('nisn', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->kelas !== '', function ($query): void {
                $query->whereHas('kelas', function ($query): void {
                    $query->where('kelas.id', $this->kelas);
                });
            })
            ->with(['kelas' => function ($query): void {
                $query->orderByPivot('tahun_id', 'desc');
            }])
            ->latest()
            ->paginate(10);
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
};
?>

<div class="flex flex-col gap-6">
    {{-- Pesan feedback --}}
    @if ($pesan !== null)
        <div class="flex items-center gap-3 px-4 py-3 rounded-[8px] border {{ $pesanTipe === 'success' ? 'bg-[#ecfdf3] border-[#a7f3d0]' : 'bg-[#f0f4ff] border-[#d0d9f0]' }}">
            <x-heroicon-m-{{ $pesanTipe === 'success' ? 'check-circle' : 'information-circle' }} class="h-5 w-5 shrink-0 {{ $pesanTipe === 'success' ? 'text-[#15803d]' : 'text-[#3b5998]' }}" />
            <p class="text-[13px] leading-[1.45] font-medium {{ $pesanTipe === 'success' ? 'text-[#15803d]' : 'text-[#3b5998]' }}">
                {{ $pesan }}
            </p>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-[20px] font-bold leading-[1.3] text-[#1a2233]">Daftar Siswa</h2>
        <x-button variant="primary" size="md" wire:click="tambah">
            <x-heroicon-m-plus class="h-4 w-4 -ml-0.5" />
            Tambah Siswa
        </x-button>
    </div>

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
                                    <button
                                        type="button"
                                        wire:click="edit({{ $siswa->id }})"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-md text-[#66758c] hover:text-[#15803d] hover:bg-[#ecfdf3] transition-colors duration-150"
                                        title="Edit"
                                    >
                                        <x-heroicon-o-pencil-square class="h-4 w-4" />
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="konfirmasiHapus({{ $siswa->id }})"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-md text-[#66758c] hover:text-[#c13e45] hover:bg-[#fff0f0] transition-colors duration-150"
                                        title="Hapus"
                                    >
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

    {{-- Modal Form Siswa --}}
    <x-modal id="modal-form-siswa" title="{{ $siswaId ? 'Edit Siswa' : 'Tambah Siswa' }}" size="lg">
        <div class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input
                    name="nisn"
                    label="NISN"
                    placeholder="Masukkan 10 digit NISN"
                    required
                    wire:model="nisn"
                />
                <x-input
                    name="nama"
                    label="Nama Lengkap"
                    placeholder="Masukkan nama lengkap"
                    required
                    wire:model="nama"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-select name="jenis_kelamin" label="Jenis Kelamin" required wire:model="jenis_kelamin">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </x-select>
                <x-input
                    name="tanggal_lahir"
                    type="date"
                    label="Tanggal Lahir"
                    required
                    wire:model="tanggal_lahir"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-select name="formKelasId" label="Kelas" required wire:model="formKelasId">
                    <option value="">Pilih Kelas</option>
                    @foreach ($this->daftarKelas as $kelasItem)
                        <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama_kelas }}</option>
                    @endforeach
                </x-select>
                <x-select name="formTahunId" label="Tahun Ajaran" required wire:model="formTahunId">
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach ($this->daftarTahun as $tahunItem)
                        <option value="{{ $tahunItem->id }}">{{ $tahunItem->tahun_ajaran }} &mdash; Semester {{ ucfirst($tahunItem->semester) }}</option>
                    @endforeach
                </x-select>
            </div>

            <x-textarea
                name="alamat"
                label="Alamat"
                placeholder="Masukkan alamat lengkap"
                rows="3"
                wire:model="alamat"
            />
        </div>

        <x-slot:footer>
            <x-button variant="secondary" size="md" wire:click="tutupModal">
                Batal
            </x-button>
            <x-button variant="primary" size="md" wire:click="simpan" wire:loading.attr="disabled" wire:target="simpan">
                <x-heroicon-m-check class="h-4 w-4 -ml-0.5" />
                Simpan
            </x-button>
        </x-slot:footer>
    </x-modal>

    {{-- Dialog Konfirmasi Hapus --}}
    <x-confirm-dialog
        id="konfirmasi-hapus-siswa"
        title="Hapus Siswa?"
        variant="danger"
        confirm-label="Hapus"
        cancel-label="Batal"
        on-confirm="hapusSekarang"
    >
        Data siswa <span class="font-semibold text-[#1a2233]">{{ $siswaHapusNama }}</span> akan dihapus permanen beserta seluruh nilai yang terkait. Tindakan ini tidak dapat dibatalkan.
    </x-confirm-dialog>
</div>
