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

        {{-- Tabel interaktif (search, filter kelas, pagination) --}}
        <livewire:siswa-table />
    </div>
@endsection
