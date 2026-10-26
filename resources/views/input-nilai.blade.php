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

        {{-- Form interaktif (filter kelas/mapel/tahun, input nilai, simpan) --}}
        <livewire:input-nilai-table />
    </div>
@endsection
