<?php

use App\Http\Controllers\DashboardController;
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

    Route::get('/input-nilai', function () {
        return view('input-nilai');
    })->name('input-nilai');

    Route::get('/rekap-nilai', function () {
        return view('rekap-nilai');
    })->name('rekap-nilai');

    Route::get('/laporan', function () {
        return view('laporan');
    })->name('laporan');
});
