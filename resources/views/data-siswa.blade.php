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
    {{-- Tabel interaktif (search, filter kelas, pagination, CRUD) --}}
    <livewire:siswa-table />
@endsection
