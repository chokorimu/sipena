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

        <livewire:laporan-table />

        {{-- Footer Actions --}}
        <div class="flex items-center gap-3">
            <x-button variant="primary" size="md" disabled title="Export PDF akan tersedia pada pembaruan berikutnya">
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
