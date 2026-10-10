@extends('layout.app')

@section('title', 'Rekap Nilai - SIPENA')

@section('topbar')
    <x-topbar title="Rekap Nilai">
        <x-breadcrumb>
            <x-breadcrumb-item label="Home" href="/" />
            <x-breadcrumb-item label="Rekap Nilai" :active="true" />
        </x-breadcrumb>
    </x-topbar>
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-[20px] font-bold leading-[1.3] text-[#1a2233]">Rekap Nilai Siswa</h2>
            <div class="flex items-center gap-3">
                <x-button variant="primary" size="md">
                    <x-heroicon-m-arrow-down-tray class="h-4 w-4 -ml-0.5" />
                    Export Excel
                </x-button>
                <x-button variant="secondary" size="md">
                    <x-heroicon-m-printer class="h-4 w-4 -ml-0.5" />
                    Cetak
                </x-button>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
            <div class="flex-1 w-full sm:w-auto">
                <x-select name="kelas_rekap" label="Kelas">
                    <option value="">Pilih Kelas</option>
                    <option value="VII-A" selected>VII-A</option>
                    <option value="VII-B">VII-B</option>
                    <option value="VIII-A">VIII-A</option>
                    <option value="VIII-B">VIII-B</option>
                    <option value="IX-A">IX-A</option>
                    <option value="IX-B">IX-B</option>
                </x-select>
            </div>
            <div class="flex-1 w-full sm:w-auto">
                <x-select name="semester_rekap" label="Semester">
                    <option value="">Pilih Semester</option>
                    <option value="ganjil-2024" selected>Semester Ganjil 2024/2025</option>
                    <option value="genap-2024">Semester Genap 2024/2025</option>
                </x-select>
            </div>
            <div class="flex-1 w-full sm:w-auto">
                <x-select name="mapel_rekap" label="Mata Pelajaran">
                    <option value="">Pilih Mata Pelajaran</option>
                    <option value="matematika" selected>Matematika</option>
                    <option value="ipa">IPA</option>
                    <option value="ips">IPS</option>
                    <option value="bahasa-indonesia">Bahasa Indonesia</option>
                    <option value="bahasa-inggris">Bahasa Inggris</option>
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
                    <p class="text-[24px] font-bold leading-[1.3] text-[#1a2233]">78.5</p>
                </div>
            </div>

            {{-- Nilai Tertinggi --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#ecfdf3] text-[#15803d] shrink-0">
                    <x-heroicon-m-arrow-trending-up class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Nilai Tertinggi</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#15803d]">95.0</p>
                </div>
            </div>

            {{-- Nilai Terendah --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#fff0f0] text-[#c13e45] shrink-0">
                    <x-heroicon-m-arrow-trending-down class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Nilai Terendah</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#c13e45]">45.0</p>
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
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">NIS</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 80px">Tugas</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 80px">UTS</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 80px">UAS</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 90px">Rata-rata</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 90px">Predikat</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 110px">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dfe6ef]">
                        @php
                            $rekapData = [
                                ['no' => 1, 'nama' => 'Ahmad Fauzi', 'nis' => '2024001', 'tugas' => 85, 'uts' => 78, 'uas' => 82, 'rata' => 81.7, 'predikat' => 'A', 'status' => 'Tuntas'],
                                ['no' => 2, 'nama' => 'Siti Nurhaliza', 'nis' => '2024002', 'tugas' => 90, 'uts' => 88, 'uas' => 95, 'rata' => 91.0, 'predikat' => 'A', 'status' => 'Tuntas'],
                                ['no' => 3, 'nama' => 'Muhammad Rizki', 'nis' => '2024003', 'tugas' => 75, 'uts' => 70, 'uas' => 78, 'rata' => 74.3, 'predikat' => 'B+', 'status' => 'Tuntas'],
                                ['no' => 4, 'nama' => 'Aisyah Putri', 'nis' => '2024004', 'tugas' => 80, 'uts' => 75, 'uas' => 80, 'rata' => 78.3, 'predikat' => 'B+', 'status' => 'Tuntas'],
                                ['no' => 5, 'nama' => 'Budi Santoso', 'nis' => '2024005', 'tugas' => 70, 'uts' => 65, 'uas' => 72, 'rata' => 69.0, 'predikat' => 'B', 'status' => 'Tuntas'],
                                ['no' => 6, 'nama' => 'Dewi Lestari', 'nis' => '2024006', 'tugas' => 88, 'uts' => 85, 'uas' => 90, 'rata' => 87.7, 'predikat' => 'A', 'status' => 'Tuntas'],
                                ['no' => 7, 'nama' => 'Farhan Maulana', 'nis' => '2024007', 'tugas' => 65, 'uts' => 60, 'uas' => 68, 'rata' => 64.3, 'predikat' => 'C+', 'status' => 'Tuntas'],
                                ['no' => 8, 'nama' => 'Gita Rahmawati', 'nis' => '2024008', 'tugas' => 55, 'uts' => 50, 'uas' => 45, 'rata' => 50.0, 'predikat' => 'C', 'status' => 'Belum Tuntas'],
                                ['no' => 9, 'nama' => 'Hadi Prasetyo', 'nis' => '2024009', 'tugas' => 60, 'uts' => 58, 'uas' => 62, 'rata' => 60.0, 'predikat' => 'C+', 'status' => 'Belum Tuntas'],
                                ['no' => 10, 'nama' => 'Indah Permata', 'nis' => '2024010', 'tugas' => 92, 'uts' => 90, 'uas' => 88, 'rata' => 90.0, 'predikat' => 'A', 'status' => 'Tuntas'],
                            ];
                        @endphp

                        @foreach ($rekapData as $index => $siswa)
                            @php
                                $predikatColors = match ($siswa['predikat']) {
                                    'A' => 'bg-[#ecfdf3] text-[#15803d]',
                                    'B+' => 'bg-[#ecfdf3] text-[#15803d]',
                                    'B' => 'bg-[#f0f4ff] text-[#3b5998]',
                                    'C+' => 'bg-[#fff5df] text-[#a56708]',
                                    'C' => 'bg-[#fff5df] text-[#a56708]',
                                    'D' => 'bg-[#fff0f0] text-[#c13e45]',
                                    default => 'bg-[#f3f6fa] text-[#66758c]',
                                };
                                $statusColors = $siswa['status'] === 'Tuntas'
                                    ? 'bg-[#ecfdf3] text-[#15803d]'
                                    : 'bg-[#fff0f0] text-[#c13e45]';
                            @endphp
                            <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }} hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                                <td class="px-5 py-3 text-[#1a2233]">{{ $siswa['no'] }}</td>
                                <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $siswa['nama'] }}</td>
                                <td class="px-5 py-3 text-[#66758c]">{{ $siswa['nis'] }}</td>
                                <td class="px-5 py-3 text-center text-[#1a2233]">{{ $siswa['tugas'] }}</td>
                                <td class="px-5 py-3 text-center text-[#1a2233]">{{ $siswa['uts'] }}</td>
                                <td class="px-5 py-3 text-center text-[#1a2233]">{{ $siswa['uas'] }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-semibold text-[#1a2233]">{{ number_format($siswa['rata'], 1) }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full {{ $predikatColors }}">{{ $siswa['predikat'] }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full {{ $statusColors }}">{{ $siswa['status'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="flex items-center justify-between px-5 py-3 border-t border-[#dfe6ef] bg-[#f8f9fb] rounded-b-[12px]">
                <p class="text-[13px] leading-[1.45] text-[#66758c]">Menampilkan <span class="font-medium text-[#1a2233]">1-10</span> dari <span class="font-medium text-[#1a2233]">32</span> data</p>
                <div class="flex items-center gap-1">
                    {{-- Previous --}}
                    <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-[#96a2b4] bg-white border border-[#dfe6ef] cursor-not-allowed opacity-50" disabled>
                        <x-heroicon-m-chevron-left class="h-4 w-4" />
                    </button>

                    {{-- Page Numbers --}}
                    <button type="button" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-md text-[13px] font-medium bg-[#15803d] text-white border border-[#15803d]">1</button>
                    <button type="button" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-md text-[13px] font-medium text-[#1a2233] bg-white border border-[#dfe6ef] hover:bg-[#f3f6fa] transition-colors duration-150">2</button>
                    <button type="button" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-md text-[13px] font-medium text-[#1a2233] bg-white border border-[#dfe6ef] hover:bg-[#f3f6fa] transition-colors duration-150">3</button>
                    <button type="button" class="inline-flex items-center justify-center h-8 min-w-[32px] px-2 rounded-md text-[13px] font-medium text-[#1a2233] bg-white border border-[#dfe6ef] hover:bg-[#f3f6fa] transition-colors duration-150">4</button>

                    {{-- Next --}}
                    <button type="button" class="inline-flex items-center justify-center h-8 w-8 rounded-md text-[#1a2233] bg-white border border-[#dfe6ef] hover:bg-[#f3f6fa] transition-colors duration-150">
                        <x-heroicon-m-chevron-right class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
