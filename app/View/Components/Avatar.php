<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Avatar extends Component
{
    /**
     * @param  'xs'|'sm'|'md'|'lg'  $size
     */
    public function __construct(
        public string $name = '',
        public ?string $src = null,
        public string $size = 'md',
    ) {}

    public function initials(): string
    {
        $words = explode(' ', trim($this->name));

        if (count($words) >= 2) {
            return mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1));
        }

        return mb_strtoupper(mb_substr($this->name, 0, 2));
    }

    public function sizeClasses(): string
    {
        return match ($this->size) {
            'xs' => 'h-6 w-6 text-[10px]',
            'sm' => 'h-8 w-8 text-[12px]',
            'md' => 'h-10 w-10 text-[12px]',
            'lg' => 'h-12 w-12 text-[13px]',
            default => 'h-10 w-10 text-[12px]',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.avatar', [
            'sizeClass' => $this->sizeClasses(),
            'initialsText' => $this->initials(),
        ]);
    }
}
