@extends('layout.app')

@section('title', 'Input Nilai - SIPENA')

@section('topbar')
    <x-topbar title="Input Nilai">
        <x-breadcrumb>
            <x-breadcrumb-item label="Home" href="/" />
            <x-breadcrumb-item label="Input Nilai" :active="true" />
        </x-breadcrumb>
    </x-topbar>
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        {{-- Page Header --}}
        <h2 class="text-[20px] font-bold leading-[1.3] text-[#1a2233]">Input Nilai Siswa</h2>

        {{-- Filter Bar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
            <div class="flex-1 w-full sm:w-auto">
                <x-select name="kelas_filter" label="Kelas">
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
                <x-select name="mapel_filter" label="Mata Pelajaran">
                    <option value="">Pilih Mata Pelajaran</option>
                    <option value="matematika" selected>Matematika</option>
                    <option value="ipa">IPA</option>
                    <option value="ips">IPS</option>
                    <option value="bahasa-indonesia">Bahasa Indonesia</option>
                    <option value="bahasa-inggris">Bahasa Inggris</option>
                </x-select>
            </div>
            <div class="flex-1 w-full sm:w-auto">
                <x-select name="semester_filter" label="Semester">
                    <option value="">Pilih Semester</option>
                    <option value="ganjil-2024" selected>Semester Ganjil 2024/2025</option>
                    <option value="genap-2024">Semester Genap 2024/2025</option>
                </x-select>
            </div>
            <x-button variant="primary" size="md">
                Tampilkan
            </x-button>
        </div>

        {{-- Info Alert --}}
        <div class="flex items-center gap-3 px-4 py-3 rounded-[8px] bg-[#f0f4ff] border border-[#d0d9f0]">
            <x-heroicon-m-information-circle class="h-5 w-5 text-[#3b5998] shrink-0" />
            <p class="text-[13px] leading-[1.45] text-[#3b5998] font-medium">
                Kelas VII-A &bull; Matematika &bull; Semester Ganjil 2024/2025
            </p>
        </div>

        {{-- Input Nilai Table --}}
        <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
            <div class="overflow-x-auto">
                <table class="w-full text-[13px] leading-[1.45]">
                    <thead>
                        <tr class="border-b border-[#dfe6ef] bg-[#f8f9fb]">
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 60px">No</th>
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">NIS</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">Tugas</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">UTS</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">UAS</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">Rata-rata</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 100px">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dfe6ef]">
                        @php
                            $nilaiData = [
                                ['no' => 1, 'nama' => 'Ahmad Fauzi', 'nis' => '2024001', 'tugas' => 85, 'uts' => 78, 'uas' => 82, 'rata' => 81.7, 'predikat' => 'A'],
                                ['no' => 2, 'nama' => 'Siti Nurhaliza', 'nis' => '2024002', 'tugas' => 90, 'uts' => 88, 'uas' => 92, 'rata' => 90.0, 'predikat' => 'A'],
                                ['no' => 3, 'nama' => 'Muhammad Rizki', 'nis' => '2024003', 'tugas' => 75, 'uts' => 70, 'uas' => 78, 'rata' => 74.3, 'predikat' => 'B+'],
                                ['no' => 4, 'nama' => 'Aisyah Putri', 'nis' => '2024004', 'tugas' => 80, 'uts' => 75, 'uas' => 80, 'rata' => 78.3, 'predikat' => 'B+'],
                                ['no' => 5, 'nama' => 'Budi Santoso', 'nis' => '2024005', 'tugas' => 70, 'uts' => 65, 'uas' => 72, 'rata' => 69.0, 'predikat' => 'B'],
                                ['no' => 6, 'nama' => 'Dewi Lestari', 'nis' => '2024006', 'tugas' => 88, 'uts' => 85, 'uas' => 90, 'rata' => 87.7, 'predikat' => 'A'],
                                ['no' => 7, 'nama' => 'Farhan Maulana', 'nis' => '2024007', 'tugas' => 65, 'uts' => 60, 'uas' => 68, 'rata' => 64.3, 'predikat' => 'C+'],
                                ['no' => 8, 'nama' => 'Gita Rahmawati', 'nis' => '2024008', 'tugas' => null, 'uts' => null, 'uas' => null, 'rata' => null, 'predikat' => null],
                                ['no' => 9, 'nama' => 'Hadi Prasetyo', 'nis' => '2024009', 'tugas' => null, 'uts' => null, 'uas' => null, 'rata' => null, 'predikat' => null],
                                ['no' => 10, 'nama' => 'Indah Permata', 'nis' => '2024010', 'tugas' => null, 'uts' => null, 'uas' => null, 'rata' => null, 'predikat' => null],
                            ];
                        @endphp

                        @foreach ($nilaiData as $index => $siswa)
                            @php
                                $predikatColors = match ($siswa['predikat']) {
                                    'A' => 'bg-[#ecfdf3] text-[#15803d]',
                                    'B+' => 'bg-[#ecfdf3] text-[#15803d]',
                                    'B' => 'bg-[#f0f4ff] text-[#3b5998]',
                                    'C+' => 'bg-[#fff5df] text-[#a56708]',
                                    'C' => 'bg-[#fff5df] text-[#a56708]',
                                    'D' => 'bg-[#fff0f0] text-[#c13e45]',
                                    default => '',
                                };
                            @endphp
                            <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }}">
                                <td class="px-5 py-3 text-[#1a2233]">{{ $siswa['no'] }}</td>
                                <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $siswa['nama'] }}</td>
                                <td class="px-5 py-3 text-[#66758c]">{{ $siswa['nis'] }}</td>
                                <td class="px-5 py-2.5 text-center">
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value="{{ $siswa['tugas'] }}"
                                        placeholder="-"
                                        class="w-full h-[34px] px-2 text-center text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] bg-white border border-[#dfe6ef] rounded-[6px] focus:outline-none focus:border-[#15803d] focus:ring-1 focus:ring-[#15803d]/20"
                                    />
                                </td>
                                <td class="px-5 py-2.5 text-center">
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value="{{ $siswa['uts'] }}"
                                        placeholder="-"
                                        class="w-full h-[34px] px-2 text-center text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] bg-white border border-[#dfe6ef] rounded-[6px] focus:outline-none focus:border-[#15803d] focus:ring-1 focus:ring-[#15803d]/20"
                                    />
                                </td>
                                <td class="px-5 py-2.5 text-center">
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value="{{ $siswa['uas'] }}"
                                        placeholder="-"
                                        class="w-full h-[34px] px-2 text-center text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] bg-white border border-[#dfe6ef] rounded-[6px] focus:outline-none focus:border-[#15803d] focus:ring-1 focus:ring-[#15803d]/20"
                                    />
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($siswa['rata'] !== null)
                                        <span class="text-[13px] font-semibold text-[#1a2233]">{{ number_format($siswa['rata'], 1) }}</span>
                                    @else
                                        <span class="text-[13px] text-[#96a2b4]">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($siswa['predikat'])
                                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full {{ $predikatColors }}">{{ $siswa['predikat'] }}</span>
                                    @else
                                        <span class="text-[13px] text-[#96a2b4]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center gap-3">
            <x-button variant="primary" size="md">
                <x-heroicon-m-check class="h-4 w-4 -ml-0.5" />
                Simpan Nilai
            </x-button>
            <x-button variant="secondary" size="md">
                Reset
            </x-button>
        </div>
    </div>
@endsection
