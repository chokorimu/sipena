<?php

namespace App\Services;

use App\Models\Nilai;
use App\Services\Ai\AiManager;
use App\Services\Ai\AiResult;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class EvaluasiAiService
{
    /**
     * Batas karakter draft agar muat di kolom evaluasi (validasi SFC: max 2000).
     */
    private const MAKS_KARAKTER = 1800;

    public function __construct(private readonly AiManager $manager) {}

    /**
     * Label provider AI yang sedang aktif (untuk tooltip tombol di UI).
     */
    public function namaProvider(): string
    {
        return $this->manager->label();
    }

    /**
     * Buat draft catatan evaluasi wali kelas lewat provider AI yang aktif.
     *
     * Nama siswa sengaja TIDAK dikirim ke API — catatan rapor memakai sebutan
     * "Ananda", jadi yang dikirim hanya nilai, kelas, dan semester.
     *
     * @param  Collection<int, Nilai>  $nilai
     * @return array{sukses: bool, teks: string, pesan: string}
     */
    public function draftEvaluasi(Collection $nilai, ?float $rataRata, string $status, ?string $predikat, string $kelasNama, string $tahunAjaran): array
    {
        try {
            $provider = $this->manager->provider();
        } catch (InvalidArgumentException $e) {
            return AiResult::gagal($e->getMessage())->keArray();
        }

        $hasil = $provider->buatTeks(
            $this->susunPrompt($nilai, $rataRata, $status, $predikat, $kelasNama, $tahunAjaran)
        );

        if (! $hasil->sukses) {
            return $hasil->keArray();
        }

        $teks = $hasil->teks;

        if (mb_strlen($teks) > self::MAKS_KARAKTER) {
            $teks = $this->potongDiAkhirKalimat($teks, self::MAKS_KARAKTER);
        }

        return AiResult::ok($teks)->keArray();
    }

    /**
     * Potong teks di akhir kalimat terakhir sebelum limit, supaya draf tidak
     * berhenti di tengah kalimat.
     */
    private function potongDiAkhirKalimat(string $teks, int $limit): string
    {
        $potongan = mb_substr($teks, 0, $limit);

        $posisi = max(
            strrpos($potongan, '.') ?: 0,
            strrpos($potongan, '!') ?: 0,
            strrpos($potongan, '?') ?: 0,
            strrpos($potongan, "\n") ?: 0,
        );

        return rtrim(mb_substr($potongan, 0, $posisi + 1));
    }

    /**
     * Susun prompt bahasa Indonesia untuk catatan evaluasi wali kelas.
     *
     * @param  Collection<int, Nilai>  $nilai
     */
    private function susunPrompt(Collection $nilai, ?float $rataRata, string $status, ?string $predikat, string $kelasNama, string $tahunAjaran): string
    {
        $barisNilai = $nilai
            ->map(fn (Nilai $item): string => sprintf('- %s: %s', $item->mataPelajaran->nama_mapel, number_format((float) $item->nilai_akhir, 1)))
            ->implode("\n");

        $rataRataTeks = $rataRata !== null ? number_format($rataRata, 1) : '-';
        $predikatTeks = $predikat ?? '-';

        return <<<PROMPT
            Kamu adalah wali kelas di MTs (madrasah tsanawiyah) yang sedang menulis catatan evaluasi untuk rapor siswa.

            Data nilai siswa:
            Kelas: {$kelasNama}
            Semester: {$tahunAjaran}
            KKM (kriteria ketuntasan minimal): 75

            Nilai akhir per mata pelajaran:
            {$barisNilai}

            Rata-rata: {$rataRataTeks}
            Predikat: {$predikatTeks}
            Status ketuntasan: {$status}

            Tulis catatan evaluasi dengan ketentuan:
            - Panggil siswa dengan sebutan "Ananda", jangan pernah menyebut nama
            - 1 sampai 2 paragraf, maksimal 1200 karakter
            - Sebutkan mata pelajaran dengan nilai tinggi sebagai kekuatan siswa
            - Sebutkan mata pelajaran yang nilainya di bawah KKM beserta saran perbaikan yang konkret
            - Nada hangat, memotivasi, dan profesional seperti wali kelas
            - Akhiri dengan kalimat motivasi
            - Output HANYA teks catatan: tanpa judul, tanpa tanda kutip, tanpa markdown, tanpa penjelasan tambahan
            PROMPT;
    }
}
