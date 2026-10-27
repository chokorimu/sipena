<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanExportController extends Controller
{
    /**
     * Export laporan rekap kelas (statistik + ringkasan per mapel) sebagai CSV.
     * Parameter kosong memakai default yang sama dengan halaman laporan.
     */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'kelas' => ['nullable', 'integer', 'exists:kelas,id'],
            'tahun' => ['nullable', 'integer', 'exists:tahun_ajaran,id'],
        ]);

        $kelasId = $validated['kelas'] ?? Kelas::query()->orderBy('nama_kelas')->value('id');
        $tahunId = $validated['tahun'] ?? TahunAjaran::query()->orderByDesc('id')->value('id');

        $kelas = Kelas::query()->findOrFail($kelasId);
        $tahun = TahunAjaran::query()->findOrFail($tahunId);

        $siswaIds = Siswa::query()
            ->whereHas('kelas', function ($query) use ($kelasId, $tahunId): void {
                $query->where('kelas.id', $kelasId)
                    ->where('kelas_siswa.tahun_id', $tahunId);
            })
            ->pluck('id');

        $nilaiKelas = Nilai::query()
            ->with('mataPelajaran')
            ->where('tahun_id', $tahunId)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->filter(fn (Nilai $nilai): bool => $nilai->nilai_akhir !== null);

        $rataPerSiswa = $nilaiKelas
            ->groupBy('siswa_id')
            ->map(fn ($nilaiSiswa): float => round((float) $nilaiSiswa->avg('nilai_akhir'), 2));

        $semuaNilai = $nilaiKelas->pluck('nilai_akhir')->map(fn ($nilai): float => (float) $nilai);

        $statistik = [
            'totalSiswa' => $siswaIds->count(),
            'tuntas' => $rataPerSiswa->filter(fn (float $rata): bool => $rata >= Nilai::KKM)->count(),
            'belumTuntas' => $rataPerSiswa->filter(fn (float $rata): bool => $rata < Nilai::KKM)->count(),
            'rataRata' => $semuaNilai->isNotEmpty() ? round($semuaNilai->avg(), 1) : null,
        ];

        $ringkasanMapel = $nilaiKelas
            ->groupBy('mapel_id')
            ->map(function ($nilaiMapel): array {
                $nilaiAkhir = $nilaiMapel->pluck('nilai_akhir')->map(fn ($nilai): float => (float) $nilai);

                return [
                    'mapel' => $nilaiMapel->first()->mataPelajaran->nama_mapel,
                    'rata' => round($nilaiAkhir->avg(), 1),
                    'tertinggi' => round($nilaiAkhir->max(), 1),
                    'terendah' => round($nilaiAkhir->min(), 1),
                    'tuntas' => $nilaiAkhir->filter(fn (float $nilai): bool => $nilai >= Nilai::KKM)->count(),
                    'belum' => $nilaiAkhir->filter(fn (float $nilai): bool => $nilai < Nilai::KKM)->count(),
                ];
            })
            ->sortBy('mapel')
            ->values();

        $namaFile = Str::slug("laporan-{$kelas->nama_kelas}").'.csv';

        return response()->streamDownload(function () use ($kelas, $tahun, $statistik, $ringkasanMapel): void {
            $handle = fopen('php://output', 'wb');

            // BOM UTF-8 supaya Excel Windows membaca karakter Indonesia dengan benar.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ["Laporan Akademik - Kelas {$kelas->nama_kelas}"]);
            fputcsv($handle, ["Tahun Ajaran: {$tahun->tahun_ajaran} Semester ".ucfirst($tahun->semester)]);
            fputcsv($handle, []);

            fputcsv($handle, ['Statistik', 'Jumlah']);
            fputcsv($handle, ['Total Siswa', $statistik['totalSiswa']]);
            fputcsv($handle, ['Tuntas (rata-rata >= KKM '.Nilai::KKM.')', $statistik['tuntas']]);
            fputcsv($handle, ['Belum Tuntas', $statistik['belumTuntas']]);
            fputcsv($handle, ['Rata-rata Kelas', $statistik['rataRata'] !== null ? number_format($statistik['rataRata'], 1, ',', '.') : '-']);
            fputcsv($handle, []);

            fputcsv($handle, ['Mata Pelajaran', 'Rata-rata', 'Tertinggi', 'Terendah', 'Tuntas', 'Belum Tuntas']);

            foreach ($ringkasanMapel as $mapel) {
                fputcsv($handle, [
                    $mapel['mapel'],
                    number_format($mapel['rata'], 1, ',', '.'),
                    number_format($mapel['tertinggi'], 1, ',', '.'),
                    number_format($mapel['terendah'], 1, ',', '.'),
                    $mapel['tuntas'],
                    $mapel['belum'],
                ]);
            }

            fclose($handle);
        }, $namaFile, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
