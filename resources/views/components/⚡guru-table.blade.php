<?php

use App\Models\Guru;
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

    // Form modal guru
    public ?int $guruId = null;

    public string $nip = '';

    public string $nama_guru = '';

    public string $jenis_kelamin = 'L';

    public string $no_hp = '';

    public string $alamat = '';

    // Konfirmasi hapus
    public ?int $guruHapusId = null;

    public string $guruHapusNama = '';

    // Feedback
    public ?string $pesan = null;

    public string $pesanTipe = 'success';

    /**
     * Reset halaman ketika filter berubah.
     */
    public function updated(string $property): void
    {
        if ($property === 'search') {
            $this->resetPage();
        }
    }

    public function tambah(): void
    {
        $this->resetForm();

        $this->dispatch('open-modal', id: 'modal-form-guru');
    }

    public function edit(int $id): void
    {
        $guru = Guru::query()->findOrFail($id);

        $this->resetForm();

        $this->guruId = $guru->id;
        $this->nip = $guru->nip;
        $this->nama_guru = $guru->nama_guru;
        $this->jenis_kelamin = $guru->jenis_kelamin;
        $this->no_hp = (string) $guru->no_hp;
        $this->alamat = (string) $guru->alamat;

        $this->dispatch('open-modal', id: 'modal-form-guru');
    }

    public function simpan(): void
    {
        $this->validate(
            [
                'nip' => ['required', 'digits:18', Rule::unique('guru', 'nip')->ignore($this->guruId)],
                'nama_guru' => ['required', 'string', 'max:255'],
                'jenis_kelamin' => ['required', 'in:L,P'],
                'no_hp' => ['nullable', 'string', 'max:20'],
                'alamat' => ['nullable', 'string'],
            ],
            [
                'nip.required' => 'NIP wajib diisi.',
                'nip.digits' => 'NIP harus terdiri dari 18 angka.',
                'nip.unique' => 'NIP sudah terdaftar.',
                'nama_guru.required' => 'Nama guru wajib diisi.',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            ]
        );

        $data = [
            'nip' => $this->nip,
            'nama_guru' => $this->nama_guru,
            'jenis_kelamin' => $this->jenis_kelamin,
            'no_hp' => $this->no_hp,
            'alamat' => $this->alamat,
        ];

        if ($this->guruId !== null) {
            $guru = Guru::query()->findOrFail($this->guruId);
            $guru->update($data);
            $pesan = 'Data guru berhasil diperbarui.';
        } else {
            Guru::create($data);
            $pesan = 'Guru baru berhasil ditambahkan.';
        }

        $this->dispatch('close-modal', id: 'modal-form-guru');
        $this->resetForm();
        $this->pesan = $pesan;
        $this->pesanTipe = 'success';
    }

    public function konfirmasiHapus(int $id): void
    {
        $guru = Guru::query()->findOrFail($id);

        $this->guruHapusId = $guru->id;
        $this->guruHapusNama = $guru->nama_guru;

        $this->dispatch('open-confirm', id: 'konfirmasi-hapus-guru');
    }

    public function hapusSekarang(): void
    {
        if ($this->guruHapusId === null) {
            return;
        }

        $guru = Guru::query()->findOrFail($this->guruHapusId);
        $nama = $guru->nama_guru;
        $guru->delete();

        $this->dispatch('close-confirm', id: 'konfirmasi-hapus-guru');
        $this->reset(['guruHapusId', 'guruHapusNama']);
        $this->pesan = "Guru {$nama} berhasil dihapus.";
        $this->pesanTipe = 'success';
    }

    public function batalHapus(): void
    {
        $this->dispatch('close-confirm', id: 'konfirmasi-hapus-guru');
        $this->reset(['guruHapusId', 'guruHapusNama']);
    }

    public function tutupModal(): void
    {
        $this->dispatch('close-modal', id: 'modal-form-guru');
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['guruId', 'nip', 'nama_guru', 'no_hp', 'alamat']);
        $this->jenis_kelamin = 'L';
        $this->resetValidation();
    }

    #[Computed]
    public function guruList()
    {
        return Guru::query()
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('nama_guru', 'like', '%'.$this->search.'%')
                        ->orWhere('nip', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(10);
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
        <h2 class="text-[20px] font-bold leading-[1.3] text-[#1a2233]">Daftar Guru</h2>
        <x-button variant="primary" size="md" wire:click="tambah">
            <x-heroicon-m-plus class="h-4 w-4 -ml-0.5" />
            Tambah Guru
        </x-button>
    </div>

    {{-- Filters & Search --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="flex-1 w-full sm:w-auto">
            <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIP guru..." />
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
        <div class="overflow-x-auto">
            <table class="w-full text-[13px] leading-[1.45]">
                <thead>
                    <tr class="border-b border-[#dfe6ef] bg-[#f8f9fb]">
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 60px">No</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Nama Guru</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">NIP</th>
                        <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">No. HP</th>
                        <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dfe6ef]">
                    @forelse ($this->guruList as $index => $guru)
                        <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }} hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                            <td class="px-5 py-3 text-[#1a2233]">{{ $this->guruList->firstItem() + $index }}</td>
                            <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $guru->nama_guru }}</td>
                            <td class="px-5 py-3 text-[#66758c]">{{ $guru->nip }}</td>
                            <td class="px-5 py-3 text-[#66758c]">{{ $guru->no_hp ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $guru->id }})"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-md text-[#66758c] hover:text-[#15803d] hover:bg-[#ecfdf3] transition-colors duration-150"
                                        title="Edit"
                                    >
                                        <x-heroicon-o-pencil-square class="h-4 w-4" />
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="konfirmasiHapus({{ $guru->id }})"
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
                                Tidak ada data guru yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @php
            $currentPage = $this->guruList->currentPage();
            $lastPage = $this->guruList->lastPage();
            $pages = collect(range(max(1, $currentPage - 1), min($lastPage, $currentPage + 1)));
        @endphp
        <div class="flex items-center justify-between px-5 py-3 border-t border-[#dfe6ef] bg-[#f8f9fb] rounded-b-[12px]">
            <p class="text-[13px] leading-[1.45] text-[#66758c]">
                Menampilkan <span class="font-medium text-[#1a2233]">{{ $this->guruList->firstItem() ?? 0 }}-{{ $this->guruList->lastItem() ?? 0 }}</span>
                dari <span class="font-medium text-[#1a2233]">{{ $this->guruList->total() }}</span> data
            </p>
            @if ($lastPage > 1)
                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        wire:click="previousPage"
                        @disabled($this->guruList->onFirstPage())
                        class="inline-flex items-center justify-center h-8 w-8 rounded-[6px] text-[#66758c] transition-colors duration-150 {{ $this->guruList->onFirstPage() ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#f3f6fa]' }}"
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

    {{-- Modal Form Guru --}}
    <x-modal id="modal-form-guru" title="{{ $guruId ? 'Edit Guru' : 'Tambah Guru' }}" size="lg">
        <div class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input
                    name="nip"
                    label="NIP"
                    placeholder="Masukkan 18 digit NIP"
                    required
                    wire:model="nip"
                />
                <x-input
                    name="nama_guru"
                    label="Nama Lengkap"
                    placeholder="Masukkan nama lengkap"
                    required
                    wire:model="nama_guru"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-select name="jenis_kelamin" label="Jenis Kelamin" required wire:model="jenis_kelamin">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </x-select>
                <x-input
                    name="no_hp"
                    label="No. HP"
                    placeholder="Contoh: 081234567890"
                    wire:model="no_hp"
                />
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
        id="konfirmasi-hapus-guru"
        title="Hapus Guru?"
        variant="danger"
        confirm-label="Hapus"
        cancel-label="Batal"
        on-confirm="hapusSekarang"
    >
        Data guru <span class="font-semibold text-[#1a2233]">{{ $guruHapusNama }}</span> akan dihapus permanen. Jadwal mengajar yang terkait juga akan dihapus, dan kelas yang menjadi wali kelasnya akan kehilangan wali. Tindakan ini tidak dapat dibatalkan.
    </x-confirm-dialog>
</div>
