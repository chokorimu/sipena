<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts::guest')]
#[Title('Daftar - SIPENA')]
class extends Component
{
    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public bool $showPassword = false;

    /**
     * Tolak akses saat pendaftaran dinonaktifkan lewat config.
     */
    public function mount(): void
    {
        abort_unless(config('sipena.registrasi_dibuka'), 403, 'Pendaftaran akun sedang ditutup.');
    }

    public function togglePassword(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function register(): void
    {
        $this->name = trim($this->name);
        $this->username = trim($this->username);
        $this->email = trim($this->email);

        $validated = $this->validate(
            [
                'name' => ['required', 'string', 'max:100'],
                'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:users,username'],
                'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ],
            [
                'name.required' => 'Nama lengkap wajib diisi.',
                'name.max' => 'Nama lengkap maksimal 100 karakter.',
                'username.required' => 'Username wajib diisi.',
                'username.min' => 'Username minimal 3 karakter.',
                'username.max' => 'Username maksimal 30 karakter.',
                'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
                'username.unique' => 'Username sudah dipakai, pilih yang lain.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 255 karakter.',
                'email.unique' => 'Email sudah terdaftar.',
                'password.required' => 'Kata sandi wajib diisi.',
                'password.min' => 'Kata sandi minimal 8 karakter.',
            ]
        );

        if ($validated['password'] !== $this->passwordConfirmation) {
            $this->addError('password', 'Konfirmasi kata sandi tidak cocok.');

            return;
        }

        // Akun hasil pendaftaran mandiri selalu berperan guru, bukan admin.
        $user = User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] !== null && $validated['email'] !== '' ? $validated['email'] : null,
            'password' => $validated['password'],
            'role' => 'guru',
        ]);

        Auth::login($user);

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: false);
    }
};
?>

<div class="min-h-screen flex flex-col items-center justify-center p-16 bg-[#f3f6fa]">
    {{-- Panel Registrasi --}}
    <div class="w-full max-w-[520px] bg-white border border-[#dfe6ef] rounded-[12px] shadow-[0px_8px_32px_0px_rgba(23,43,77,0.03)] p-10 flex flex-col gap-8">

        {{-- Identitas Sekolah --}}
        <div class="flex items-center gap-2 h-12">
            <div class="flex items-center justify-center h-10 w-10 rounded-md shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="">
            </div>
            <div class="flex flex-col justify-center overflow-hidden">
                <span class="text-[11px] leading-[1.6] text-[#66758c]">MTs Negeri Ambon</span>
                <span class="text-[13px] font-bold leading-[1.45] text-[#1a2233]">SiPena</span>
            </div>
        </div>

        {{-- Pengantar Registrasi --}}
        <div class="flex flex-col gap-2 w-full">
            <h1 class="text-[28px] font-bold leading-[1.3] text-[#15803d]">
                Buat Akun SiPena
            </h1>
            <p class="text-[12px] leading-[1.6] text-[#66758c]">
                Lengkapi data di bawah untuk mendaftar sebagai guru.
            </p>
        </div>

        {{-- Formulir Registrasi --}}
        <form wire:submit="register" class="flex flex-col gap-6 w-full">
            <div class="flex flex-col gap-5 w-full">
                {{-- Nama Lengkap --}}
                <div class="flex flex-col gap-[7px] w-full">
                    <label for="name" class="text-[12px] font-semibold leading-[1.45] text-[#1a2233]">
                        Nama Lengkap
                    </label>
                    <input
                        type="text"
                        id="name"
                        wire:model="name"
                        placeholder="Masukkan nama lengkap"
                        class="w-full h-[42px] px-3 bg-white border rounded-[8px] text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('name') ? 'border-[#c13e45] focus:border-[#c13e45] focus:ring-[#c13e45]/20' : 'border-[#dfe6ef] focus:border-[#15803d] focus:ring-[#15803d]/20' }}"
                    />
                    @error('name')
                        <p class="text-[11px] leading-[1.6] text-[#c13e45]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Username --}}
                <div class="flex flex-col gap-[7px] w-full">
                    <label for="username" class="text-[12px] font-semibold leading-[1.45] text-[#1a2233]">
                        Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        wire:model="username"
                        placeholder="Contoh: siti.nurhaliza"
                        class="w-full h-[42px] px-3 bg-white border rounded-[8px] text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('username') ? 'border-[#c13e45] focus:border-[#c13e45] focus:ring-[#c13e45]/20' : 'border-[#dfe6ef] focus:border-[#15803d] focus:ring-[#15803d]/20' }}"
                    />
                    @error('username')
                        <p class="text-[11px] leading-[1.6] text-[#c13e45]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="flex flex-col gap-[7px] w-full">
                    <label for="email" class="text-[12px] font-semibold leading-[1.45] text-[#1a2233]">
                        Email <span class="font-normal text-[#96a2b4]">(opsional)</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        placeholder="nama@mtsnambon.sch.id"
                        class="w-full h-[42px] px-3 bg-white border rounded-[8px] text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('email') ? 'border-[#c13e45] focus:border-[#c13e45] focus:ring-[#c13e45]/20' : 'border-[#dfe6ef] focus:border-[#15803d] focus:ring-[#15803d]/20' }}"
                    />
                    @error('email')
                        <p class="text-[11px] leading-[1.6] text-[#c13e45]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kata Sandi --}}
                <div class="flex flex-col gap-[7px] w-full">
                    <label for="password" class="text-[12px] font-semibold leading-[1.45] text-[#1a2233]">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <input
                            type="{{ $showPassword ? 'text' : 'password' }}"
                            id="password"
                            wire:model="password"
                            placeholder="Minimal 8 karakter"
                            class="w-full h-[42px] px-3 pr-10 bg-white border rounded-[8px] text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('password') ? 'border-[#c13e45] focus:border-[#c13e45] focus:ring-[#c13e45]/20' : 'border-[#dfe6ef] focus:border-[#15803d] focus:ring-[#15803d]/20' }}"
                        />
                        <button
                            type="button"
                            wire:click="togglePassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#96a2b4] hover:text-[#66758c] transition-colors duration-150"
                        >
                            @if ($showPassword)
                                <x-heroicon-m-eye class="h-[18px] w-[18px]" />
                            @else
                                <x-heroicon-m-eye-slash class="h-[18px] w-[18px]" />
                            @endif
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px] leading-[1.6] text-[#c13e45]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Kata Sandi --}}
                <div class="flex flex-col gap-[7px] w-full">
                    <label for="password_confirmation" class="text-[12px] font-semibold leading-[1.45] text-[#1a2233]">
                        Konfirmasi Kata Sandi
                    </label>
                    <input
                        type="{{ $showPassword ? 'text' : 'password' }}"
                        id="password_confirmation"
                        wire:model="passwordConfirmation"
                        placeholder="Ulangi kata sandi"
                        class="w-full h-[42px] px-3 bg-white border rounded-[8px] text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('password') ? 'border-[#c13e45] focus:border-[#c13e45] focus:ring-[#c13e45]/20' : 'border-[#dfe6ef] focus:border-[#15803d] focus:ring-[#15803d]/20' }}"
                    />
                </div>
            </div>

            {{-- Tombol Daftar --}}
            <button
                type="submit"
                class="w-full h-12 flex items-center justify-center bg-[#15803d] border border-[#15803d] rounded-[8px] text-[13px] font-semibold text-white hover:bg-[#166534] active:bg-[#14532d] transition-colors duration-150"
            >
                Daftar
            </button>
        </form>

        {{-- Tautan ke Login --}}
        <p class="text-[12px] leading-[1.6] text-[#66758c] text-center">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-[#15803d] hover:underline">
                Masuk di sini
            </a>
        </p>
    </div>

    {{-- Identitas Layanan --}}
    <p class="mt-6 text-[11px] font-normal leading-[1.45] text-[#66758c] text-center">
        SiPena &middot; MTs Negeri Ambon
    </p>
</div>
