@extends('layout.app')

@section('title', 'Laporan - SIPENA')

@section('topbar')
    <x-topbar title="Laporan">
        <x-breadcrumb>
            <x-breadcrumb-item label="Home" href="/" />
            <x-breadcrumb-item label="Laporan" :active="true" />
        </x-breadcrumb>
    </x-topbar>
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        {{-- Page Header --}}
        <h2 class="text-[20px] font-bold leading-[1.3] text-[#1a2233]">Laporan Akademik</h2>

        {{-- Filter Bar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
            <div class="flex-1 w-full sm:w-auto">
                <x-select name="kelas_laporan" label="Kelas">
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
                <x-select name="semester_laporan" label="Semester">
                    <option value="">Pilih Semester</option>
                    <option value="ganjil-2024" selected>Semester Ganjil 2024/2025</option>
                    <option value="genap-2024">Semester Genap 2024/2025</option>
                </x-select>
            </div>
            <div class="flex-1 w-full sm:w-auto">
                <x-select name="jenis_laporan" label="Jenis Laporan">
                    <option value="">Pilih Jenis Laporan</option>
                    <option value="rekap-kelas" selected>Rekap Kelas</option>
                    <option value="rapor-siswa">Rapor Siswa</option>
                    <option value="analisis-nilai">Analisis Nilai</option>
                </x-select>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Siswa --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#f0f4ff] text-[#3b5998] shrink-0">
                    <x-heroicon-m-users class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Total Siswa</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#1a2233]">32</p>
                </div>
            </div>

            {{-- Tuntas --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#ecfdf3] text-[#15803d] shrink-0">
                    <x-heroicon-m-check-circle class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Tuntas</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#15803d]">28</p>
                </div>
            </div>

            {{-- Belum Tuntas --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#fff0f0] text-[#c13e45] shrink-0">
                    <x-heroicon-m-x-circle class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Belum Tuntas</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#c13e45]">4</p>
                </div>
            </div>

            {{-- Rata-rata --}}
            <div class="flex items-center gap-4 p-5 rounded-[12px] bg-white border border-[#dfe6ef]">
                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-[#fff5df] text-[#a56708] shrink-0">
                    <x-heroicon-m-chart-bar class="h-5 w-5" />
                </div>
                <div class="flex flex-col gap-0.5">
                    <p class="text-[13px] leading-[1.45] text-[#66758c]">Rata-rata</p>
                    <p class="text-[24px] font-bold leading-[1.3] text-[#1a2233]">78.5</p>
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
                        @php
                            $mapelData = [
                                ['no' => 1, 'mapel' => 'Matematika', 'rata' => 78.5, 'tertinggi' => 95, 'terendah' => 45, 'tuntas' => 28, 'belum' => 4],
                                ['no' => 2, 'mapel' => 'IPA', 'rata' => 80.2, 'tertinggi' => 92, 'terendah' => 50, 'tuntas' => 30, 'belum' => 2],
                                ['no' => 3, 'mapel' => 'IPS', 'rata' => 75.8, 'tertinggi' => 90, 'terendah' => 48, 'tuntas' => 26, 'belum' => 6],
                                ['no' => 4, 'mapel' => 'Bahasa Indonesia', 'rata' => 82.1, 'tertinggi' => 96, 'terendah' => 55, 'tuntas' => 30, 'belum' => 2],
                                ['no' => 5, 'mapel' => 'Bahasa Inggris', 'rata' => 76.4, 'tertinggi' => 88, 'terendah' => 42, 'tuntas' => 25, 'belum' => 7],
                                ['no' => 6, 'mapel' => 'PKn', 'rata' => 81.0, 'tertinggi' => 94, 'terendah' => 52, 'tuntas' => 29, 'belum' => 3],
                                ['no' => 7, 'mapel' => 'Seni Budaya', 'rata' => 84.3, 'tertinggi' => 98, 'terendah' => 60, 'tuntas' => 31, 'belum' => 1],
                                ['no' => 8, 'mapel' => 'Penjaskes', 'rata' => 85.7, 'tertinggi' => 95, 'terendah' => 65, 'tuntas' => 32, 'belum' => 0],
                            ];
                        @endphp

                        @foreach ($mapelData as $index => $mapel)
                            <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }} hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                                <td class="px-5 py-3 text-[#1a2233]">{{ $mapel['no'] }}</td>
                                <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $mapel['mapel'] }}</td>
                                <td class="px-5 py-3 text-center font-semibold text-[#1a2233]">{{ number_format($mapel['rata'], 1) }}</td>
                                <td class="px-5 py-3 text-center text-[#15803d] font-medium">{{ $mapel['tertinggi'] }}</td>
                                <td class="px-5 py-3 text-center text-[#c13e45] font-medium">{{ $mapel['terendah'] }}</td>
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
                        @endforeach
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
                    @php
                        $predikatDist = [
                            ['label' => 'A', 'jumlah' => 8, 'total' => 32, 'variant' => 'primary'],
                            ['label' => 'B+', 'jumlah' => 10, 'total' => 32, 'variant' => 'primary'],
                            ['label' => 'B', 'jumlah' => 7, 'total' => 32, 'variant' => 'info'],
                            ['label' => 'C+', 'jumlah' => 5, 'total' => 32, 'variant' => 'warning'],
                            ['label' => 'C', 'jumlah' => 2, 'total' => 32, 'variant' => 'danger'],
                        ];
                    @endphp

                    @foreach ($predikatDist as $item)
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Predikat {{ $item['label'] }}</span>
                                <span class="text-[13px] leading-[1.45] text-[#66758c]">{{ $item['jumlah'] }} siswa</span>
                            </div>
                            <x-progress-bar
                                :value="round($item['jumlah'] / $item['total'] * 100)"
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
                    <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">Catatan & Rekomendasi</h3>
                </div>
                <div class="p-5 flex flex-col divide-y divide-[#dfe6ef]">
                    {{-- Item 1 --}}
                    <div class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-[8px] bg-[#ecfdf3] text-[#15803d] shrink-0">
                            <x-heroicon-m-check class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <p class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Pencapaian KKM Baik</p>
                            <p class="text-[12px] leading-[1.6] text-[#66758c]">87.5% siswa telah mencapai KKM pada semua mata pelajaran. Pertahankan metode pembelajaran saat ini.</p>
                        </div>
                    </div>

                    {{-- Item 2 --}}
                    <div class="flex items-start gap-3 py-3 last:pb-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-[8px] bg-[#fff5df] text-[#a56708] shrink-0">
                            <x-heroicon-m-exclamation-triangle class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <p class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Perhatian pada Bahasa Inggris</p>
                            <p class="text-[12px] leading-[1.6] text-[#66758c]">7 siswa belum tuntas pada mata pelajaran Bahasa Inggris. Disarankan untuk memberikan jam tambahan atau bimbingan khusus.</p>
                        </div>
                    </div>

                    {{-- Item 3 --}}
                    <div class="flex items-start gap-3 py-3 last:pb-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-[8px] bg-[#ecfdf3] text-[#15803d] shrink-0">
                            <x-heroicon-m-check class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <p class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Penjaskes Terbaik</p>
                            <p class="text-[12px] leading-[1.6] text-[#66758c]">Seluruh siswa tuntas pada mata pelajaran Penjaskes dengan rata-rata tertinggi 85.7.</p>
                        </div>
                    </div>

                    {{-- Item 4 --}}
                    <div class="flex items-start gap-3 py-3 last:pb-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-[8px] bg-[#fff5df] text-[#a56708] shrink-0">
                            <x-heroicon-m-exclamation-triangle class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <p class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Remedial Diperlukan</p>
                            <p class="text-[12px] leading-[1.6] text-[#66758c]">4 siswa memerlukan program remedial pada mata pelajaran Matematika untuk mencapai KKM.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center gap-3">
            <x-button variant="primary" size="md">
                <x-heroicon-m-arrow-down-tray class="h-4 w-4 -ml-0.5" />
                Download PDF
            </x-button>
            <x-button variant="secondary" size="md">
                <x-heroicon-m-arrow-down-tray class="h-4 w-4 -ml-0.5" />
                Download Excel
            </x-button>
            <x-button variant="secondary" size="md">
                <x-heroicon-m-printer class="h-4 w-4 -ml-0.5" />
                Cetak Laporan
            </x-button>
        </div>
    </div>
@endsection
