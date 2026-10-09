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
    </div>
@endsection
