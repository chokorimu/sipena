<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapExportController extends Controller
{
    /**
     * Export rekap nilai kelas (mapel & tahun terpilih) sebagai CSV.
     * Parameter kosong memakai default yang sama dengan halaman rekap.
     */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'kelas' => ['nullable', 'integer', 'exists:kelas,id'],
            'mapel' => ['nullable', 'integer', 'exists:mata_pelajaran,id'],
            'tahun' => ['nullable', 'integer', 'exists:tahun_ajaran,id'],
        ]);

        $kelasId = $validated['kelas'] ?? Kelas::query()->orderBy('nama_kelas')->value('id');
        $mapelId = $validated['mapel'] ?? MataPelajaran::query()->orderBy('nama_mapel')->value('id');
        $tahunId = $validated['tahun'] ?? TahunAjaran::query()->orderByDesc('id')->value('id');

        $kelas = Kelas::query()->findOrFail($kelasId);
        $mapel = MataPelajaran::query()->findOrFail($mapelId);
        $tahun = TahunAjaran::query()->findOrFail($tahunId);

        $siswaList = Siswa::query()
            ->whereHas('kelas', function ($query) use ($kelasId, $tahunId): void {
                $query->where('kelas.id', $kelasId)
                    ->where('kelas_siswa.tahun_id', $tahunId);
            })
            ->orderBy('nama')
            ->get();

        $nilaiRekap = Nilai::query()
            ->where('mapel_id', $mapelId)
            ->where('tahun_id', $tahunId)
            ->whereIn('siswa_id', $siswaList->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        $rankings = Nilai::hitungRanking($nilaiRekap);

        $namaFile = Str::slug("rekap-nilai-{$kelas->nama_kelas}-{$mapel->nama_mapel}").'.csv';

        return response()->streamDownload(function () use ($siswaList, $nilaiRekap, $rankings): void {
            $handle = fopen('php://output', 'wb');

            // BOM UTF-8 supaya Excel Windows membaca karakter Indonesia dengan benar.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Peringkat',
                'NIS',
                'Nama Siswa',
                'Formatif',
                'Sumatif',
                'Nilai Akhir',
                'Predikat',
                'Status',
            ]);

            foreach ($siswaList as $siswa) {
                $nilai = $nilaiRekap->get($siswa->id);

                fputcsv($handle, [
                    $rankings[$siswa->id] ?? '',
                    $siswa->nisn,
                    $siswa->nama,
                    $nilai?->formatif !== null ? number_format((float) $nilai->formatif, 2, ',', '.') : '',
                    $nilai?->sumatif !== null ? number_format((float) $nilai->sumatif, 2, ',', '.') : '',
                    $nilai?->nilai_akhir !== null ? number_format((float) $nilai->nilai_akhir, 2, ',', '.') : '',
                    $nilai?->predikat() ?? '',
                    $nilai?->status_kelulusan ?? '',
                ]);
            }

            fclose($handle);
        }, $namaFile, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
