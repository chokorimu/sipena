<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanExportController;
use App\Http\Controllers\RekapExportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages::auth.login')->middleware('guest')->name('login');

Route::post('/logout', function () {
    Auth::logout();

    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/data-siswa', function () {
        return view('data-siswa');
    })->name('data-siswa');

    Route::get('/data-guru', function () {
        return view('data-guru');
    })->name('data-guru');

    Route::get('/input-nilai', function () {
        return view('input-nilai');
    })->name('input-nilai');

    Route::get('/rekap-nilai/export', [RekapExportController::class, 'export'])->name('rekap-nilai.export');

    Route::get('/rekap-nilai', function () {
        return view('rekap-nilai');
    })->name('rekap-nilai');

    Route::get('/laporan/export', [LaporanExportController::class, 'export'])->name('laporan.export');

    Route::get('/laporan', function () {
        return view('laporan');
    })->name('laporan');
});
