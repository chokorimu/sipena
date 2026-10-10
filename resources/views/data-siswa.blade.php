@extends('layout.app')

@section('title', 'Data Siswa - SIPENA')

@section('topbar')
    <x-topbar title="Data Siswa">
        <x-breadcrumb>
            <x-breadcrumb-item label="Home" href="/" />
            <x-breadcrumb-item label="Data Siswa" :active="true" />
        </x-breadcrumb>
    </x-topbar>
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-[20px] font-bold leading-[1.3] text-[#1a2233]">Daftar Siswa</h2>
            <x-button variant="primary" size="md">
                <x-heroicon-m-plus class="h-4 w-4 -ml-0.5" />
                Tambah Siswa
            </x-button>
        </div>

        {{-- Filters & Search --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="flex-1 w-full sm:w-auto">
                <x-search-input placeholder="Cari nama atau NIS siswa..." />
            </div>
            <div class="flex items-center gap-3">
                <x-select name="kelas">
                    <option value="">Semua Kelas</option>
                    <option value="VII-A">VII-A</option>
                    <option value="VII-B">VII-B</option>
                    <option value="VIII-A">VIII-A</option>
                    <option value="VIII-B">VIII-B</option>
                    <option value="VIII-C">VIII-C</option>
                    <option value="IX-A">IX-A</option>
                    <option value="IX-B">IX-B</option>
                </x-select>
                <x-select name="status">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="tidak-aktif">Tidak Aktif</option>
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
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dfe6ef]">
                        @php
                            $siswaList = [
                                ['no' => 1, 'nama' => 'Ahmad Fauzi', 'nis' => '2024001', 'kelas' => 'VII-A', 'status' => 'Aktif'],
                                ['no' => 2, 'nama' => 'Siti Nurhaliza', 'nis' => '2024002', 'kelas' => 'VII-B', 'status' => 'Aktif'],
                                ['no' => 3, 'nama' => 'Muhammad Rizki', 'nis' => '2024003', 'kelas' => 'VIII-A', 'status' => 'Aktif'],
                                ['no' => 4, 'nama' => 'Aisyah Putri', 'nis' => '2024004', 'kelas' => 'VIII-C', 'status' => 'Tidak Aktif'],
                                ['no' => 5, 'nama' => 'Budi Santoso', 'nis' => '2024005', 'kelas' => 'IX-A', 'status' => 'Aktif'],
                                ['no' => 6, 'nama' => 'Dewi Lestari', 'nis' => '2024006', 'kelas' => 'VII-A', 'status' => 'Aktif'],
                                ['no' => 7, 'nama' => 'Farhan Maulana', 'nis' => '2024007', 'kelas' => 'VIII-B', 'status' => 'Aktif'],
                                ['no' => 8, 'nama' => 'Gita Rahmawati', 'nis' => '2024008', 'kelas' => 'IX-B', 'status' => 'Aktif'],
                                ['no' => 9, 'nama' => 'Hadi Prasetyo', 'nis' => '2024009', 'kelas' => 'VII-B', 'status' => 'Tidak Aktif'],
                                ['no' => 10, 'nama' => 'Indah Permata', 'nis' => '2024010', 'kelas' => 'VIII-A', 'status' => 'Aktif'],
                            ];
                        @endphp

                        @foreach ($siswaList as $index => $siswa)
                            <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }} hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                                <td class="px-5 py-3 text-[#1a2233]">{{ $siswa['no'] }}</td>
                                <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $siswa['nama'] }}</td>
                                <td class="px-5 py-3 text-[#66758c]">{{ $siswa['nis'] }}</td>
                                <td class="px-5 py-3 text-[#66758c]">{{ $siswa['kelas'] }}</td>
                                <td class="px-5 py-3 text-center">
                                    @if ($siswa['status'] === 'Aktif')
                                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#ecfdf3] text-[#15803d]">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#fff0f0] text-[#c13e45]">Tidak Aktif</span>
                                    @endif
                                </td>
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="flex items-center justify-between px-5 py-3 border-t border-[#dfe6ef] bg-[#f8f9fb] rounded-b-[12px]">
                <p class="text-[13px] leading-[1.45] text-[#66758c]">Menampilkan <span class="font-medium text-[#1a2233]">1-10</span> dari <span class="font-medium text-[#1a2233]">1.248</span> data</p>
                <div class="flex items-center gap-1">
                    {{-- Previous --}}
                    <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-[#96a2b4] bg-white border border-[#dfe6ef] cursor-not-allowed opacity-50" disabled>
                        <x-heroicon-m-chevron-left class="h-4 w-4" />
                    </button>

                    {{-- Page Numbers --}}
                    <button type="button" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-md text-[13px] font-medium bg-[#15803d] text-white border border-[#15803d]">1</button>
                    <button type="button" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-md text-[13px] font-medium text-[#1a2233] bg-white border border-[#dfe6ef] hover:bg-[#f3f6fa] transition-colors duration-150">2</button>
                    <button type="button" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-md text-[13px] font-medium text-[#1a2233] bg-white border border-[#dfe6ef] hover:bg-[#f3f6fa] transition-colors duration-150">3</button>
                    <span class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 text-[13px] text-[#96a2b4]">...</span>
                    <button type="button" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-md text-[13px] font-medium text-[#1a2233] bg-white border border-[#dfe6ef] hover:bg-[#f3f6fa] transition-colors duration-150">125</button>

                    {{-- Next --}}
                    <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-[#1a2233] bg-white border border-[#dfe6ef] hover:bg-[#f3f6fa] transition-colors duration-150">
                        <x-heroicon-m-chevron-right class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
