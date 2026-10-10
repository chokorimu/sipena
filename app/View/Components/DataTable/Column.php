<?php

namespace App\View\Components\DataTable;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Column extends Component
{
    /**
     * @param  string  $label  Header label kolom
     * @param  string|null  $field  Nama field dari data row
     * @param  bool  $sortable  Apakah kolom bisa diurutkan
     * @param  'left'|'center'|'right'  $align  Alignment konten
     * @param  string|null  $width  Lebar kolom (e.g. 'w-12', '120px')
     */
    public function __construct(
        public string $label = '',
        public ?string $field = null,
        public bool $sortable = false,
        public string $align = 'left',
        public ?string $width = null,
    ) {}

    public function alignmentClasses(): string
    {
        return match ($this->align) {
            'center' => 'text-center',
            'right' => 'text-right',
            default => 'text-left',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.data-table.column', [
            'alignClass' => $this->alignmentClasses(),
        ]);
    }
}
