<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages::auth.login')->name('login');

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

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
