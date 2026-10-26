<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new
#[Layout('layouts::guest')]
#[Title('Login - SIPENA')]
class extends Component
{
    #[Validate('required')]
    public string $username = '';

    #[Validate('required')]
    public string $password = '';

    public bool $showPassword = false;

    public function togglePassword(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function login(): void
    {
        $this->validate();

        // Login logic akan ditambahkan nanti
    }
};
?>

<div class="min-h-screen flex flex-col items-center justify-center p-16 bg-[#f3f6fa]">
    {{-- Panel Login --}}
    <div class="w-full max-w-[520px] bg-white border border-[#dfe6ef] rounded-[12px] shadow-[0px_8px_32px_0px_rgba(23,43,77,0.03)] p-10 flex flex-col gap-8">

        {{-- Identitas Sekolah --}}
        <div class="flex items-center gap-2 h-12">
            <div class="h-12 w-12 shrink-0 rounded-[8px] bg-[#15803d] flex items-center justify-center">
                <span class="text-white font-bold text-[18px]">SP</span>
            </div>
            <div class="flex flex-col justify-center overflow-hidden">
                <span class="text-[11px] leading-[1.6] text-[#66758c]">MTs Negeri Ambon</span>
                <span class="text-[13px] font-bold leading-[1.45] text-[#1a2233]">SiPena</span>
            </div>
        </div>

        {{-- Pemisah --}}

        {{-- Pengantar Login --}}
        <div class="flex flex-col gap-2 w-full">
            <h1 class="text-[28px] font-bold leading-[1.3] text-[#15803d]">
                Masuk ke SiPena
            </h1>
        </div>

        {{-- Formulir Login --}}
        <form wire:submit="login" class="flex flex-col gap-6 w-full">
            {{-- Kredensial Akun --}}
            <div class="flex flex-col gap-5 w-full">
                {{-- Username --}}
                <div class="flex flex-col gap-[7px] w-full">
                    <label for="username" class="text-[12px] font-semibold leading-[1.45] text-[#1a2233]">
                        Username
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            id="username"
                            wire:model="username"
                            placeholder="Masukkan username"
                            class="w-full h-[42px] px-3 bg-white border rounded-[8px] text-[13px] leading-[1.45] text-[#1a2233] placeholder:text-[#96a2b4] focus:outline-none focus:ring-2 focus:ring-offset-0 {{ $errors->has('username') ? 'border-[#c13e45] focus:border-[#c13e45] focus:ring-[#c13e45]/20' : 'border-[#dfe6ef] focus:border-[#15803d] focus:ring-[#15803d]/20' }}"
                        />
                    </div>
                    @error('username')
                        <p class="text-[11px] leading-[1.6] text-[#c13e45]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kata Sandi --}}
                <div class="flex flex-col gap-[7px] w-full">
                    <label for="password" class="text-[12px] font-semibold leading-[1.45] text-[#1a2233]">
                        Kata sandi
                    </label>
                    <div class="relative">
                        <input
                            type="{{ $showPassword ? 'text' : 'password' }}"
                            id="password"
                            wire:model="password"
                            placeholder="Masukkan kata sandi"
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
            </div>

            {{-- Tombol Masuk --}}
            <button
                type="submit"
                class="w-full h-12 flex items-center justify-center bg-[#15803d] border border-[#15803d] rounded-[8px] text-[13px] font-semibold text-white hover:bg-[#166534] active:bg-[#14532d] transition-colors duration-150"
            >
                Masuk
            </button>
        </form>

        {{-- Bantuan Akses --}}
        {{-- <div class="w-full flex gap-3 items-start bg-[#ecfdf3] rounded-[8px] p-4">
            <span class="shrink-0 text-[#15803d]">
                <x-heroicon-m-question-mark-circle class="h-5 w-5" />
            </span>
            <div class="flex flex-col gap-1 min-w-0">
                <p class="text-[12px] font-semibold leading-[1.45] text-[#1a2233]">
                    Butuh bantuan akses?
                </p>
                <p class="text-[11px] font-normal leading-[1.6] text-[#66758c]">
                    Hubungi administrator sekolah jika Anda belum memiliki akun atau mengalami kendala masuk.
                </p>
            </div>
        </div> --}}
    </div>

    {{-- Identitas Layanan --}}
    <p class="mt-6 text-[11px] font-normal leading-[1.45] text-[#66758c] text-center">
        SiPena &middot; MTs Negeri Ambon
    </p>
</div>
