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
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div class="flex flex-col gap-1">
                <h2 class="text-[20px] font-bold leading-[1.3] text-[#1a2233]">Rekap Nilai Siswa</h2>
                <p class="text-[13px] leading-[1.45] text-[#66758c]">
                    Daftar nilai seluruh siswa untuk <span class="font-medium text-[#1a2233]">satu mata pelajaran</span> pada kelas &amp; tahun ajaran terpilih, lengkap dengan peringkat.
                </p>
            </div>
            <div class="flex items-center gap-3 print:hidden">
                <x-button variant="primary" size="md" :href="route('rekap-nilai.export', request()->query())">
                    <x-heroicon-m-arrow-down-tray class="h-4 w-4 -ml-0.5" />
                    Export Excel
                </x-button>
            </div>
        </div>

        <livewire:rekap-nilai-table />
    </div>
@endsection
