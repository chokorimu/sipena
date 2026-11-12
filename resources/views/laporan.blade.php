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
        <div class="flex flex-col gap-1">
            <h2 class="text-[20px] font-bold leading-[1.3] text-[#1a2233]">Laporan Akademik</h2>
            <p class="text-[13px] leading-[1.45] text-[#66758c]">
                Ringkasan dan dokumen <span class="font-medium text-[#1a2233]">tingkat kelas</span>: rekap seluruh mata pelajaran, rapor per siswa, dan analisis sebaran nilai.
            </p>
        </div>

        <livewire:laporan-table />

        {{-- Footer Actions --}}
        <div class="flex items-center gap-3 print:hidden">
            <x-button variant="primary" size="md" :href="route('laporan.pdf', request()->query())">
                <x-heroicon-m-arrow-down-tray class="h-4 w-4 -ml-0.5" />
                Download PDF
            </x-button>
            <x-button variant="secondary" size="md" :href="route('laporan.export', request()->query())">
                <x-heroicon-m-arrow-down-tray class="h-4 w-4 -ml-0.5" />
                Download Excel
            </x-button>
            <x-button variant="secondary" size="md" onclick="window.print()">
                <x-heroicon-m-printer class="h-4 w-4 -ml-0.5" />
                Cetak Laporan
            </x-button>
        </div>
    </div>
@endsection
