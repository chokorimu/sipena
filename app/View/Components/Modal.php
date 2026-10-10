<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * @param  string|null  $title  Judul modal
     * @param  'sm'|'md'|'lg'|'xl'  $size  Ukuran modal
     * @param  bool  $dismissible  Apakah bisa ditutup via overlay click / tombol X
     */
    public function __construct(
        public ?string $title = null,
        public string $size = 'md',
        public bool $dismissible = true,
    ) {}

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'sm' => 'max-w-sm',
            'md' => 'max-w-lg',
            'lg' => 'max-w-2xl',
            'xl' => 'max-w-4xl',
            default => 'max-w-lg',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.modal', [
            'sizeClass' => $this->sizeClasses(),
        ]);
    }
}
