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
                <x-button variant="secondary" size="md" onclick="window.print()">
                    <x-heroicon-m-printer class="h-4 w-4 -ml-0.5" />
                    Cetak
                </x-button>
            </div>
        </div>

        <livewire:rekap-nilai-table />
    </div>
@endsection
