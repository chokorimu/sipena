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
            <x-card-stat label="Total Siswa" :value="number_format($totalSiswa, 0, ',', '.')">
                <x-slot:icon>
                    <x-heroicon-m-user-group class="h-5 w-5" />
                </x-slot:icon>
            </x-card-stat>

            <x-card-stat label="Total Guru" :value="number_format($totalGuru, 0, ',', '.')">
                <x-slot:icon>
                    <x-heroicon-m-user class="h-5 w-5" />
                </x-slot:icon>
            </x-card-stat>

            <x-card-stat label="Mata Pelajaran" :value="number_format($totalMapel, 0, ',', '.')">
                <x-slot:icon>
                    <x-heroicon-m-book-open class="h-5 w-5" />
                </x-slot:icon>
            </x-card-stat>

            <x-card-stat label="Kelas Aktif" :value="number_format($totalKelas, 0, ',', '.')">
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
                <a href="{{ route('data-siswa') }}" class="text-[13px] font-medium text-[#15803d] hover:underline">Lihat Semua</a>
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dfe6ef]">
                        @forelse ($siswaTerbaru as $index => $siswa)
                            <tr class="{{ $index % 2 === 1 ? 'bg-[#f8f9fb]/50' : 'bg-white' }} hover:bg-[#ecfdf3]/30 transition-colors duration-100">
                                <td class="px-5 py-3 text-[#1a2233]">{{ $index + 1 }}</td>
                                <td class="px-5 py-3 text-[#1a2233] font-medium">{{ $siswa->nama }}</td>
                                <td class="px-5 py-3 text-[#66758c]">{{ $siswa->nisn }}</td>
                                <td class="px-5 py-3 text-[#66758c]">{{ $siswa->kelas->first()?->nama_kelas ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-[13px] text-[#96a2b4]">
                                    Belum ada data siswa.
                                </td>
                            </tr>
                        @endforelse
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
                    @php
                        $variantPerTingkat = ['VII' => 'primary', 'VIII' => 'info', 'IX' => 'warning'];
                    @endphp
                    @forelse ($distribusi as $item)
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[13px] leading-[1.45] text-[#1a2233] font-medium">Kelas {{ $item['tingkat'] }}</span>
                                <span class="text-[13px] leading-[1.45] text-[#66758c]">{{ $item['total'] }} siswa</span>
                            </div>
                            <x-progress-bar :value="$item['persen']" :variant="$variantPerTingkat[$item['tingkat']] ?? 'primary'" size="sm" />
                        </div>
                    @empty
                        <p class="text-[13px] text-[#96a2b4]">Belum ada data distribusi.</p>
                    @endforelse
                </div>
            </div>

            {{-- Aktivitas Terkini --}}
            <div class="bg-white rounded-[12px] shadow-sm border border-[#dfe6ef]">
                <div class="px-5 py-4 border-b border-[#dfe6ef]">
                    <h3 class="text-[16px] font-semibold leading-[1.4] text-[#1a2233]">Aktivitas Terkini</h3>
                </div>
                <div class="p-5 flex flex-col divide-y divide-[#dfe6ef]">
                    @forelse ($aktivitas as $item)
                        <div class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-[8px] bg-[#ecfdf3] text-[#15803d] shrink-0">
                                <x-heroicon-m-pencil class="h-4 w-4" />
                            </div>
                            <div class="flex flex-col gap-0.5 min-w-0">
                                <p class="text-[13px] leading-[1.45] text-[#1a2233]">
                                    Nilai {{ $item->mataPelajaran->nama_mapel }} — {{ $item->siswa->nama }} telah diperbarui
                                </p>
                                <p class="text-[11px] leading-[1.6] text-[#96a2b4]">{{ $item->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-[13px] text-[#96a2b4]">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
