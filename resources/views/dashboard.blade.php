@extends('layout.app')

@section('title', 'Dashboard - SIPENA')

@section('topbar')
    <x-topbar title="Dashboard">
        <x-breadcrumb>
            <x-breadcrumb-item label="Home" href="/" />
            <x-breadcrumb-item label="Dashboard" :active="true" />
        </x-breadcrumb>
    </x-topbar>
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card-stat label="Total Siswa" value="1.248" trend="+12%" trend-direction="up" trend-label="dari bulan lalu">
                <x-slot:icon>
                    <x-heroicon-m-user-group class="h-5 w-5" />
                </x-slot:icon>
            </x-card-stat>

            <x-card-stat label="Total Guru" value="86" trend="+3" trend-direction="up" trend-label="guru baru">
                <x-slot:icon>
                    <x-heroicon-m-user class="h-5 w-5" />
                </x-slot:icon>
            </x-card-stat>

            <x-card-stat label="Mata Pelajaran" value="24">
                <x-slot:icon>
                    <x-heroicon-m-book-open class="h-5 w-5" />
                </x-slot:icon>
            </x-card-stat>

            <x-card-stat label="Kelas Aktif" value="36" trend="-2" trend-direction="down" trend-label="dari semester lalu">
                <x-slot:icon>
                    <x-heroicon-m-building-library class="h-5 w-5" />
                </x-slot:icon>
            </x-card-stat>
        </div>

        {{-- Tabel Data Siswa Terbaru --}}
        <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#dfe6ef]">
                <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">Data Siswa Terbaru</h3>
                <a href="#" class="text-[13px] font-medium text-[#15803d] hover:underline">Lihat Semua</a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-[13px] leading-[1.45]">
                    <thead>
                        <tr class="border-b border-[#dfe6ef] bg-[#f8f9fb]">
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider" style="width: 60px">No</th>
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">NIS</th>
                            <th class="px-5 py-3 text-left text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Kelas</th>
                            <th class="px-5 py-3 text-center text-[12px] font-semibold text-[#66758c] uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dfe6ef]">
                        <tr class="bg-white hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                            <td class="px-5 py-3 text-[#1a2233]">1</td>
                            <td class="px-5 py-3 text-[#1a2233] font-medium">Ahmad Fauzi</td>
                            <td class="px-5 py-3 text-[#66758c]">2024001</td>
                            <td class="px-5 py-3 text-[#66758c]">VII-A</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#ecfdf3] text-[#15803d]">Aktif</span>
                            </td>
                        </tr>
                        <tr class="bg-[#f8f9fb]/50 hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                            <td class="px-5 py-3 text-[#1a2233]">2</td>
                            <td class="px-5 py-3 text-[#1a2233] font-medium">Siti Nurhaliza</td>
                            <td class="px-5 py-3 text-[#66758c]">2024002</td>
                            <td class="px-5 py-3 text-[#66758c]">VII-B</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#ecfdf3] text-[#15803d]">Aktif</span>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                            <td class="px-5 py-3 text-[#1a2233]">3</td>
                            <td class="px-5 py-3 text-[#1a2233] font-medium">Muhammad Rizki</td>
                            <td class="px-5 py-3 text-[#66758c]">2024003</td>
                            <td class="px-5 py-3 text-[#66758c]">VIII-A</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#ecfdf3] text-[#15803d]">Aktif</span>
                            </td>
                        </tr>
                        <tr class="bg-[#f8f9fb]/50 hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                            <td class="px-5 py-3 text-[#1a2233]">4</td>
                            <td class="px-5 py-3 text-[#1a2233] font-medium">Aisyah Putri</td>
                            <td class="px-5 py-3 text-[#66758c]">2024004</td>
                            <td class="px-5 py-3 text-[#66758c]">VIII-C</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#fff0f0] text-[#c13e45]">Tidak Aktif</span>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                            <td class="px-5 py-3 text-[#1a2233]">5</td>
                            <td class="px-5 py-3 text-[#1a2233] font-medium">Budi Santoso</td>
                            <td class="px-5 py-3 text-[#66758c]">2024005</td>
                            <td class="px-5 py-3 text-[#66758c]">IX-A</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium rounded-full bg-[#ecfdf3] text-[#15803d]">Aktif</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bottom Row: Distribusi & Aktivitas --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Distribusi Siswa per Kelas --}}
            <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
                <div class="px-5 py-4 border-b border-[#dfe6ef]">
                    <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">Distribusi Siswa per Kelas</h3>
                </div>
                <div class="p-5 flex flex-col gap-4">
                    {{-- Kelas VII --}}
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Kelas VII</span>
                            <span class="text-[13px] leading-[1.45] text-[#66758c]">420 siswa</span>
                        </div>
                        <x-progress-bar :value="34" variant="primary" size="sm" />
                    </div>

                    {{-- Kelas VIII --}}
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Kelas VIII</span>
                            <span class="text-[13px] leading-[1.45] text-[#66758c]">398 siswa</span>
                        </div>
                        <x-progress-bar :value="32" variant="info" size="sm" />
                    </div>

                    {{-- Kelas IX --}}
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Kelas IX</span>
                            <span class="text-[13px] leading-[1.45] text-[#66758c]">430 siswa</span>
                        </div>
                        <x-progress-bar :value="34" variant="warning" size="sm" />
                    </div>
                </div>
            </div>

            {{-- Aktivitas Terkini --}}
            <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
                <div class="px-5 py-4 border-b border-[#dfe6ef]">
                    <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">Aktivitas Terkini</h3>
                </div>
                <div class="p-5 flex flex-col divide-y divide-[#dfe6ef]">
                    {{-- Item 1 --}}
                    <div class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-[8px] bg-[#ecfdf3] text-[#15803d] shrink-0">
                            <x-heroicon-m-pencil class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <p class="text-[13px] leading-[1.45] text-[#1a2233]">Nilai Matematika Kelas VII-A telah diperbarui</p>
                            <p class="text-[11px] leading-[1.6] text-[#96a2b4]">2 menit lalu</p>
                        </div>
                    </div>

                    {{-- Item 2 --}}
                    <div class="flex items-start gap-3 py-3 last:pb-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-[8px] bg-[#f0f4ff] text-[#3b5998] shrink-0">
                            <x-heroicon-m-user-plus class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <p class="text-[13px] leading-[1.45] text-[#1a2233]">Siswa baru telah didaftarkan: Ahmad Fauzi</p>
                            <p class="text-[11px] leading-[1.6] text-[#96a2b4]">15 menit lalu</p>
                        </div>
                    </div>

                    {{-- Item 3 --}}
                    <div class="flex items-start gap-3 py-3 last:pb-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-[8px] bg-[#fff5df] text-[#a56708] shrink-0">
                            <x-heroicon-m-document-text class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <p class="text-[13px] leading-[1.45] text-[#1a2233]">Laporan semester ganjil telah digenerate</p>
                            <p class="text-[11px] leading-[1.6] text-[#96a2b4]">1 jam lalu</p>
                        </div>
                    </div>

                    {{-- Item 4 --}}
                    <div class="flex items-start gap-3 py-3 last:pb-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-[8px] bg-[#ecfdf3] text-[#15803d] shrink-0">
                            <x-heroicon-m-pencil class="h-4 w-4" />
                        </div>
                        <div class="flex flex-col gap-0.5 min-w-0">
                            <p class="text-[13px] leading-[1.45] text-[#1a2233]">Nilai IPA Kelas VIII-B telah diinput</p>
                            <p class="text-[11px] leading-[1.6] text-[#96a2b4]">3 jam lalu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
