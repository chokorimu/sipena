<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Skeleton extends Component
{
    /**
     * @param  'line'|'circle'|'rect'  $type  Bentuk skeleton
     * @param  string|null  $width  Lebar (class atau inline, e.g. 'w-32', '100%')
     * @param  string|null  $height  Tinggi (class atau inline, e.g. 'h-4', '200px')
     * @param  int  $count  Jumlah skeleton lines yang dirender
     */
    public function __construct(
        public string $type = 'line',
        public ?string $width = null,
        public ?string $height = null,
        public int $count = 1,
    ) {}

    public function shapeClasses(): string
    {
        return match ($this->type) {
            'circle' => 'rounded-full',
            'rect' => 'rounded-lg',
            'line' => 'rounded',
            default => 'rounded',
        };
    }

    public function defaultDimensions(): string
    {
        return match ($this->type) {
            'circle' => ($this->width ?? 'w-10').' '.($this->height ?? 'h-10'),
            'rect' => ($this->width ?? 'w-full').' '.($this->height ?? 'h-24'),
            'line' => ($this->width ?? 'w-full').' '.($this->height ?? 'h-4'),
            default => ($this->width ?? 'w-full').' '.($this->height ?? 'h-4'),
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.skeleton', [
            'shapeClass' => $this->shapeClasses(),
            'dimensionClass' => $this->defaultDimensions(),
        ]);
    }
}
