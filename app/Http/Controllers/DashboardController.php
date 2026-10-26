<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalMapel = MataPelajaran::count();
        $totalKelas = Kelas::count();

        $siswaTerbaru = Siswa::query()
            ->with(['kelas' => fn ($query) => $query->orderByPivot('tahun_id', 'desc')])
            ->latest()
            ->limit(5)
            ->get();

        $distribusi = Kelas::query()
            ->join('kelas_siswa', 'kelas.id', '=', 'kelas_siswa.kelas_id')
            ->selectRaw('kelas.tingkat, COUNT(DISTINCT kelas_siswa.siswa_id) as total')
            ->groupBy('kelas.tingkat')
            ->orderBy('kelas.tingkat')
            ->get()
            ->map(fn (Kelas $item): array => [
                'tingkat' => $item->tingkat,
                'total' => $item->total,
                'persen' => $totalSiswa > 0 ? (int) round($item->total / $totalSiswa * 100) : 0,
            ]);

        $aktivitas = Nilai::query()
            ->with(['siswa', 'mataPelajaran'])
            ->latest('updated_at')
            ->limit(4)
            ->get();

        return view('dashboard', compact(
            'totalSiswa',
            'totalGuru',
            'totalMapel',
            'totalKelas',
            'siswaTerbaru',
            'distribusi',
            'aktivitas',
        ));
    }
}
