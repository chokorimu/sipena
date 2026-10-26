@extends('layout.app')

@section('title', 'Data Guru - SIPENA')

@section('topbar')
    <x-topbar title="Data Guru">
        <x-breadcrumb>
            <x-breadcrumb-item label="Home" href="/" />
            <x-breadcrumb-item label="Data Guru" :active="true" />
        </x-breadcrumb>
    </x-topbar>
@endsection

@section('content')
    {{-- Tabel interaktif (search, pagination, CRUD) --}}
    <livewire:guru-table />
@endsection
