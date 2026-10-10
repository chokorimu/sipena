<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'SIPENA')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f3f6fa] font-sans text-[#1a2233] antialiased">
    <div class="flex min-h-screen p-4 gap-4">
        {{-- Sidebar --}}
        <x-sidebar>
            <x-sidebar-item label="Dashboard" route="dashboard" :active="request()->is('/')">
                <x-slot:icon>
                    <x-heroicon-m-home class="h-5 w-5" />
                </x-slot:icon>
            </x-sidebar-item>
            <x-sidebar-item label="Data Siswa" route="data-siswa" :active="request()->is('data-siswa')">
                <x-slot:icon>
                    <x-heroicon-m-user-group class="h-5 w-5" />
                </x-slot:icon>
            </x-sidebar-item>
            <x-sidebar-item label="Data Guru" route="#">
                <x-slot:icon>
                    <x-heroicon-m-user class="h-5 w-5" />
                </x-slot:icon>
            </x-sidebar-item>
            <x-sidebar-item label="Input Nilai" route="input-nilai" :active="request()->is('input-nilai')">
                <x-slot:icon>
                    <x-heroicon-m-pencil class="h-5 w-5" />
                </x-slot:icon>
            </x-sidebar-item>
            <x-sidebar-item label="Rekap Nilai" route="rekap-nilai" :active="request()->is('rekap-nilai')">
                <x-slot:icon>
                    <x-heroicon-m-table-cells class="h-5 w-5" />
                </x-slot:icon>
            </x-sidebar-item>
            <x-sidebar-item label="Laporan" route="laporan" :active="request()->is('laporan')">
                <x-slot:icon>
                    <x-heroicon-m-document-chart-bar class="h-5 w-5" />
                </x-slot:icon>
            </x-sidebar-item>

            <x-slot:footer>
                <div class="flex items-center gap-3">
                    <x-avatar name="Admin SIPENA" size="sm" />
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-medium text-[#1a2233] truncate">Admin</p>
                        <p class="text-[11px] text-[#66758c] truncate">Administrator</p>
                    </div>
                </div>
            </x-slot:footer>
        </x-sidebar>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-h-0 gap-4 overflow-hidden">
            @yield('topbar')
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
