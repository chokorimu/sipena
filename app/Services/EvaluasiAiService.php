<?php

namespace App\Services;

use App\Models\Nilai;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class EvaluasiAiService
{
    /**
     * Batas karakter draft agar muat di kolom evaluasi (validasi SFC: max 2000).
     */
    private const MAKS_KARAKTER = 1800;

    /**
     * Buat draft catatan evaluasi wali kelas via Gemini.
     *
     * Nama siswa sengaja TIDAK dikirim ke API — catatan rapor memakai sebutan
     * "Ananda", jadi yang dikirim hanya nilai, kelas, dan semester.
     *
     * @param  Collection<int, Nilai>  $nilai
     * @return array{sukses: bool, teks: string, pesan: string}
     */
    public function draftEvaluasi(Collection $nilai, ?float $rataRata, string $status, ?string $predikat, string $kelasNama, string $tahunAjaran): array
    {
        $apiKey = config('services.gemini.api_key');

        if (! is_string($apiKey) || $apiKey === '') {
            return [
                'sukses' => false,
                'teks' => '',
                'pesan' => 'API key Gemini belum dikonfigurasi. Tambahkan GEMINI_API_KEY di file .env.',
            ];
        }

        $model = config('services.gemini.model', 'gemini-3.6-flash');

        try {
            $response = Http::withHeaders(['x-goog-api-key' => $apiKey])
                ->timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $this->susunPrompt($nilai, $rataRata, $status, $predikat, $kelasNama, $tahunAjaran)]],
                        ],
                    ],
                    'generationConfig' => [
                        // Model 3.x menghitung thinking token ke maxOutputTokens — budget
                        // kecil bikin teks terpotong di tengah kalimat. Thinking level
                        // rendah cukup untuk task nulis evaluasi sekaligus mempercepat.
                        'maxOutputTokens' => 4096,
                        'thinkingConfig' => [
                            'thinkingLevel' => 'low',
                        ],
                    ],
                ]);
        } catch (Throwable $e) {
            Log::warning('Gemini evaluasi gagal: '.$e->getMessage());

            return [
                'sukses' => false,
                'teks' => '',
                'pesan' => 'Gagal menghubungi layanan AI. Periksa koneksi dan coba lagi.',
            ];
        }

        if ($response->failed()) {
            Log::warning('Gemini evaluasi HTTP '.$response->status().': '.mb_substr($response->body(), 0, 500));

            return [
                'sukses' => false,
                'teks' => '',
                'pesan' => match ($response->status()) {
                    429 => 'Kuota permintaan AI sudah habis untuk saat ini. Coba lagi nanti.',
                    503 => 'Layanan AI sedang sibuk (permintaan melonjak). Coba lagi beberapa saat.',
                    default => 'Gagal membuat evaluasi dengan AI (layanan menolak permintaan). Coba lagi nanti.',
                },
            ];
        }

        $json = $response->json();
        $finishReason = data_get($json, 'candidates.0.finishReason');

        if ($finishReason === 'MAX_TOKENS') {
            Log::warning('Gemini evaluasi terpotong (MAX_TOKENS) pada model '.$model);

            return [
                'sukses' => false,
                'teks' => '',
                'pesan' => 'Draf AI terpotong di tengah jalan (batas token tercapai). Coba lagi.',
            ];
        }

        $teks = trim((string) data_get($json, 'candidates.0.content.parts.0.text', ''));

        if ($teks === '') {
            return [
                'sukses' => false,
                'teks' => '',
                'pesan' => 'AI tidak mengembalikan teks evaluasi. Coba lagi.',
            ];
        }

        if (mb_strlen($teks) > self::MAKS_KARAKTER) {
            $teks = $this->potongDiAkhirKalimat($teks, self::MAKS_KARAKTER);
        }

        return [
            'sukses' => true,
            'teks' => $teks,
            'pesan' => 'Draft evaluasi berhasil dibuat. Periksa dan edit sebelum menyimpan.',
        ];
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
