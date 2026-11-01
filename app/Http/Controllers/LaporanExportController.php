<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
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
        $konteks = $this->konteks($request);

        $namaFile = Str::slug("laporan-{$konteks['kelas']->nama_kelas}").'.csv';

        return response()->streamDownload(function () use ($konteks): void {
            $handle = fopen('php://output', 'wb');

            // BOM UTF-8 supaya Excel Windows membaca karakter Indonesia dengan benar.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ["Laporan Akademik - Kelas {$konteks['kelas']->nama_kelas}"]);
            fputcsv($handle, ["Tahun Ajaran: {$konteks['tahun']->tahun_ajaran} Semester ".ucfirst($konteks['tahun']->semester)]);
            fputcsv($handle, []);

            fputcsv($handle, ['Statistik', 'Jumlah']);
            fputcsv($handle, ['Total Siswa', $konteks['statistik']['totalSiswa']]);
            fputcsv($handle, ['Tuntas (rata-rata >= KKM '.Nilai::KKM.')', $konteks['statistik']['tuntas']]);
            fputcsv($handle, ['Belum Tuntas', $konteks['statistik']['belumTuntas']]);
            fputcsv($handle, ['Rata-rata Kelas', $konteks['statistik']['rataRata'] !== null ? number_format($konteks['statistik']['rataRata'], 1, ',', '.') : '-']);
            fputcsv($handle, []);

            fputcsv($handle, ['Mata Pelajaran', 'Rata-rata', 'Tertinggi', 'Terendah', 'Tuntas', 'Belum Tuntas']);

            foreach ($konteks['ringkasanMapel'] as $mapel) {
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

    /**
     * Export laporan sebagai PDF (dompdf). Mendukung tiga jenis laporan yang
     * sama dengan halaman: rekap-kelas, rapor-siswa, analisis-nilai.
     */
    public function pdf(Request $request): Response
    {
        $validated = $request->validate([
            'jenis' => ['nullable', 'in:rekap-kelas,rapor-siswa,analisis-nilai'],
            'siswa' => ['nullable', 'integer', 'exists:siswa,id'],
            'mapel' => ['nullable', 'integer', 'exists:mata_pelajaran,id'],
        ]);

        $jenis = $validated['jenis'] ?? 'rekap-kelas';
        $konteks = $this->konteks($request);

        $data = [
            'jenis' => $jenis,
            'kelas' => $konteks['kelas'],
            'tahun' => $konteks['tahun'],
            'kkm' => Nilai::KKM,
            'statistik' => $konteks['statistik'],
            'ringkasanMapel' => $konteks['ringkasanMapel'],
            'distribusiPredikat' => $konteks['distribusiPredikat'],
        ];

        $namaFile = Str::slug("laporan-{$jenis}-{$konteks['kelas']->nama_kelas}");

        if ($jenis === 'rapor-siswa') {
            $siswa = $this->siswaRapor($request, $konteks);
            $data['rapor'] = $this->raporSiswa($siswa, $konteks['tahun']->id);
            $namaFile .= '-'.Str::slug($siswa->nama);
        } elseif ($jenis === 'analisis-nilai') {
            $mapel = $this->mapelAnalisis($request);
            $data['analisisMapel'] = $this->analisisMapel($konteks['nilaiKelas']);
            $data['sebaranNilai'] = $this->sebaranNilai($konteks['nilaiKelas'], (string) $mapel->id);
            $data['mapelNama'] = $mapel->nama_mapel;
            $data['siswaPerhatian'] = $this->siswaPerhatian($konteks['nilaiKelas']);
        }

        $pdf = Pdf::loadView("laporan.pdf.{$jenis}", $data);

        return $pdf->download($namaFile.'.pdf');
    }

    /**
     * Validasi parameter kelas/tahun & siapkan data bersama semua jenis export.
     *
     * @return array{kelas: Kelas, tahun: TahunAjaran, siswaIds: Collection<int, mixed>, nilaiKelas: Collection<int, Nilai>, statistik: array{totalSiswa: int, tuntas: int, belumTuntas: int, rataRata: float|null}, ringkasanMapel: Collection<int, array{mapel: string, rata: float, tertinggi: float, terendah: float, tuntas: int, belum: int}>, distribusiPredikat: array<int, array{label: string, jumlah: int, persen: int}>}
     */
    private function konteks(Request $request): array
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
            ->with('mataPelajaran', 'siswa')
            ->where('tahun_id', $tahunId)
            ->whereIn('siswa_id', $siswaIds)
            ->get()
            ->filter(fn (Nilai $nilai): bool => $nilai->nilai_akhir !== null);

        return [
            'kelas' => $kelas,
            'tahun' => $tahun,
            'siswaIds' => $siswaIds,
            'nilaiKelas' => $nilaiKelas,
            'statistik' => $this->statistik($siswaIds, $nilaiKelas),
            'ringkasanMapel' => $this->ringkasanMapel($nilaiKelas),
            'distribusiPredikat' => $this->distribusiPredikat($nilaiKelas),
        ];
    }

    /**
     * @return array{totalSiswa: int, tuntas: int, belumTuntas: int, rataRata: float|null}
     */
    private function statistik(Collection $siswaIds, Collection $nilaiKelas): array
    {
        $rataPerSiswa = $nilaiKelas
            ->groupBy('siswa_id')
            ->map(fn ($nilaiSiswa): float => round((float) $nilaiSiswa->avg('nilai_akhir'), 2));

        $semuaNilai = $nilaiKelas->pluck('nilai_akhir')->map(fn ($nilai): float => (float) $nilai);

        return [
            'totalSiswa' => $siswaIds->count(),
            'tuntas' => $rataPerSiswa->filter(fn (float $rata): bool => $rata >= Nilai::KKM)->count(),
            'belumTuntas' => $rataPerSiswa->filter(fn (float $rata): bool => $rata < Nilai::KKM)->count(),
            'rataRata' => $semuaNilai->isNotEmpty() ? round($semuaNilai->avg(), 1) : null,
        ];
    }

    /**
     * Ringkasan nilai per mata pelajaran.
     *
     * @return Collection<int, array{mapel: string, rata: float, tertinggi: float, terendah: float, tuntas: int, belum: int}>
     */
    private function ringkasanMapel(Collection $nilaiKelas): Collection
    {
        return $nilaiKelas
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
    }

    /**
     * Distribusi predikat A-E dari seluruh nilai kelas.
     *
     * @return array<int, array{label: string, jumlah: int, persen: int}>
     */
    private function distribusiPredikat(Collection $nilaiKelas): array
    {
        $total = $nilaiKelas->count();

        $hitung = $nilaiKelas
            ->groupBy(fn (Nilai $nilai): string => $nilai->predikat() ?? '-')
            ->map(fn ($grup): int => $grup->count());

        return collect(['A', 'B', 'C', 'D', 'E'])
            ->map(function (string $label) use ($hitung, $total): array {
                $jumlah = $hitung->get($label, 0);

                return [
                    'label' => $label,
                    'jumlah' => $jumlah,
                    'persen' => $total > 0 ? (int) round($jumlah / $total * 100) : 0,
                ];
            })
            ->all();
    }

    /**
     * Siswa untuk rapor: param siswa, atau siswa pertama di kelas kalau kosong.
     *
     * @param  array{kelas: Kelas, tahun: TahunAjaran}  $konteks
     */
    private function siswaRapor(Request $request, array $konteks): Siswa
    {
        $siswaId = $request->input('siswa');

        $query = Siswa::query()
            ->whereHas('kelas', function ($query) use ($konteks): void {
                $query->where('kelas.id', $konteks['kelas']->id)
                    ->where('kelas_siswa.tahun_id', $konteks['tahun']->id);
            });

        $siswa = $siswaId !== null && $siswaId !== ''
            ? $query->find($siswaId)
            : $query->orderBy('nama')->first();

        abort_if($siswa === null, 404, 'Siswa tidak ditemukan di kelas terpilih.');

        return $siswa;
    }

    /**
     * Mata pelajaran untuk sebaran nilai: param mapel, atau mapel pertama kalau kosong.
     */
    private function mapelAnalisis(Request $request): MataPelajaran
    {
        $mapelId = $request->input('mapel');

        $mapel = MataPelajaran::query()
            ->when($mapelId, fn ($query, $id) => $query->where('id', $id))
            ->orderBy('nama_mapel')
            ->first();

        abort_if($mapel === null, 404, 'Mata pelajaran tidak ditemukan.');

        return $mapel;
    }

    /**
     * Rapor siswa terpilih: nilai per mapel, rata-rata, predikat, dan status.
     *
     * @return array{siswa: Siswa, nilai: Collection<int, Nilai>, rataRata: float|null, status: string, predikat: string|null}
     */
    private function raporSiswa(Siswa $siswa, int $tahunId): array
    {
        $nilai = Nilai::query()
            ->with('mataPelajaran')
            ->where('tahun_id', $tahunId)
            ->where('siswa_id', $siswa->id)
            ->get()
            ->filter(fn (Nilai $item): bool => $item->nilai_akhir !== null)
            ->sortBy(fn (Nilai $item): string => $item->mataPelajaran->nama_mapel)
            ->values();

        $rataRata = $nilai->isNotEmpty() ? round((float) $nilai->avg('nilai_akhir'), 1) : null;

        $predikat = match (true) {
            $rataRata === null => null,
            $rataRata >= 90 => 'A',
            $rataRata >= 80 => 'B',
            $rataRata >= 70 => 'C',
            $rataRata >= 60 => 'D',
            default => 'E',
        };

        return [
            'siswa' => $siswa,
            'nilai' => $nilai,
            'rataRata' => $rataRata,
            'status' => $rataRata !== null && $rataRata >= Nilai::KKM ? 'Tuntas' : 'Belum Tuntas',
            'predikat' => $predikat,
        ];
    }

    /**
     * Rata-rata per mata pelajaran, urut dari tertinggi (perbandingan).
     *
     * @return Collection<int, array{mapel: string, rata: float, jumlah: int}>
     */
    private function analisisMapel(Collection $nilaiKelas): Collection
    {
        return $nilaiKelas
            ->groupBy('mapel_id')
            ->map(function ($nilaiMapel): array {
                $nilaiAkhir = $nilaiMapel->pluck('nilai_akhir')->map(fn ($nilai): float => (float) $nilai);

                return [
                    'mapel' => $nilaiMapel->first()->mataPelajaran->nama_mapel,
                    'rata' => round($nilaiAkhir->avg(), 1),
                    'jumlah' => $nilaiAkhir->count(),
                ];
            })
            ->sortByDesc('rata')
            ->values();
    }

    /**
     * Sebaran nilai (histogram 5 rentang) untuk mata pelajaran terpilih.
     *
     * @return array<int, array{label: string, jumlah: int, persen: int}>
     */
    private function sebaranNilai(Collection $nilaiKelas, string $mapelId): array
    {
        $nilaiMapel = $nilaiKelas
            ->filter(fn (Nilai $nilai): bool => (string) $nilai->mapel_id === $mapelId)
            ->pluck('nilai_akhir')
            ->map(fn ($nilai): float => (float) $nilai);

        $total = $nilaiMapel->count();

        $rentang = [
            ['label' => '90 - 100', 'min' => 90, 'max' => 101],
            ['label' => '80 - 89', 'min' => 80, 'max' => 90],
            ['label' => '70 - 79', 'min' => 70, 'max' => 80],
            ['label' => '60 - 69', 'min' => 60, 'max' => 70],
            ['label' => '0 - 59', 'min' => 0, 'max' => 60],
        ];

        return collect($rentang)
            ->map(function (array $item) use ($nilaiMapel, $total): array {
                $jumlah = $nilaiMapel
                    ->filter(fn (float $nilai): bool => $nilai >= $item['min'] && $nilai < $item['max'])
                    ->count();

                return [
                    'label' => $item['label'],
                    'jumlah' => $jumlah,
                    'persen' => $total > 0 ? (int) round($jumlah / $total * 100) : 0,
                ];
            })
            ->all();
    }

    /**
     * Siswa perlu perhatian: rata-rata seluruh mapel di bawah KKM (5 terendah).
     *
     * @return Collection<int, array{siswa: Siswa, rata: float}>
     */
    private function siswaPerhatian(Collection $nilaiKelas): Collection
    {
        return $nilaiKelas
            ->groupBy('siswa_id')
            ->map(function ($nilaiSiswa): array {
                return [
                    'siswa' => $nilaiSiswa->first()->siswa,
                    'rata' => round((float) $nilaiSiswa->avg('nilai_akhir'), 1),
                ];
            })
            ->filter(fn (array $item): bool => $item['rata'] < Nilai::KKM)
            ->sortBy('rata')
            ->take(5)
            ->values();
    }
}
