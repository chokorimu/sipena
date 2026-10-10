<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Pagination extends Component
{
    /**
     * @param  int  $currentPage  Halaman aktif saat ini
     * @param  int  $totalPages  Total jumlah halaman
     * @param  int  $perPage  Jumlah item per halaman
     * @param  int  $totalItems  Total jumlah item
     * @param  int  $maxVisiblePages  Jumlah maksimal tombol halaman yang tampil
     */
    public function __construct(
        public int $currentPage = 1,
        public int $totalPages = 1,
        public int $perPage = 10,
        public int $totalItems = 0,
        public int $maxVisiblePages = 5,
    ) {}

    /**
     * Hitung range halaman yang ditampilkan.
     *
     * @return array<int, int>
     */
    public function visiblePages(): array
    {
        if ($this->totalPages <= $this->maxVisiblePages) {
            return range(1, $this->totalPages);
        }

        $half = intdiv($this->maxVisiblePages, 2);
        $start = max(1, $this->currentPage - $half);
        $end = min($this->totalPages, $start + $this->maxVisiblePages - 1);

        if ($end - $start + 1 < $this->maxVisiblePages) {
            $start = max(1, $end - $this->maxVisiblePages + 1);
        }

        return range($start, $end);
    }

    /**
     * Info teks seperti "Menampilkan 1-10 dari 50 data".
     */
    public function infoText(): string
    {
        if ($this->totalItems === 0) {
            return 'Tidak ada data';
        }

        $from = (($this->currentPage - 1) * $this->perPage) + 1;
        $to = min($this->currentPage * $this->perPage, $this->totalItems);

        return "Menampilkan {$from}-{$to} dari {$this->totalItems} data";
    }

    public function render(): View|Closure|string
    {
        return view('components.pagination', [
            'pages' => $this->visiblePages(),
            'infoTextValue' => $this->infoText(),
        ]);
    }
}
