<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages::auth.login')->name('login');

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');
