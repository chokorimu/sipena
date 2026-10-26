<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ConfirmDialog extends Component
{
    /**
     * @param  'danger'|'warning'|'info'  $variant  Variant menentukan warna icon dan tombol konfirmasi
     * @param  string  $confirmLabel  Label tombol konfirmasi
     * @param  string  $cancelLabel  Label tombol batal
     * @param  string|null  $onConfirm  Nama aksi Livewire untuk tombol konfirmasi
     */
    public function __construct(
        public string $title = 'Konfirmasi',
        public ?string $message = null,
        public string $variant = 'danger',
        public string $confirmLabel = 'Ya, Lanjutkan',
        public string $cancelLabel = 'Batal',
        public ?string $onConfirm = null,
    ) {}

    public function iconBgClasses(): string
    {
        return match ($this->variant) {
            'danger' => 'bg-[#fff0f0]',
            'warning' => 'bg-[#fff5df]',
            'info' => 'bg-[#f0f4ff]',
            default => 'bg-[#fff0f0]',
        };
    }

    public function iconColorClasses(): string
    {
        return match ($this->variant) {
            'danger' => 'text-[#c13e45]',
            'warning' => 'text-[#a56708]',
            'info' => 'text-[#3b5998]',
            default => 'text-[#c13e45]',
        };
    }

    public function confirmButtonVariant(): string
    {
        return match ($this->variant) {
            'danger' => 'danger',
            'warning' => 'primary',
            'info' => 'primary',
            default => 'danger',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.confirm-dialog', [
            'iconBgClass' => $this->iconBgClasses(),
            'iconColorClass' => $this->iconColorClasses(),
            'confirmVariant' => $this->confirmButtonVariant(),
        ]);
    }
}
