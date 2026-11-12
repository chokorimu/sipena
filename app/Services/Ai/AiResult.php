<?php

namespace App\Services\Ai;

/**
 * Hasil pemanggilan provider AI: teks jadi, atau pesan gagal yang ramah pengguna.
 *
 * Semua perumusan pesan gagal dikumpulkan di sini supaya tiap provider
 * mengembalikan bahasa yang konsisten (dan mudah diuji).
 */
final class AiResult
{
    private function __construct(
        public readonly bool $sukses,
        public readonly string $teks,
        public readonly string $pesan,
    ) {}

    public static function ok(string $teks): self
    {
        return new self(true, $teks, 'Draft evaluasi berhasil dibuat. Periksa dan edit sebelum menyimpan.');
    }

    public static function gagal(string $pesan): self
    {
        return new self(false, '', $pesan);
    }

    /**
     * API key provider belum diisi di .env.
     */
    public static function gagalKunci(string $label, string $envKey): self
    {
        return self::gagal("API key {$label} belum dikonfigurasi. Tambahkan {$envKey} di file .env.");
    }

    /**
     * Koneksi gagal / timeout.
     */
    public static function gagalKoneksi(string $label): self
    {
        return self::gagal("Gagal menghubungi layanan AI ({$label}). Periksa koneksi dan coba lagi.");
    }

    /**
     * Provider membalas status HTTP non-2xx.
     */
    public static function gagalStatus(int $status, string $label): self
    {
        return self::gagal(match ($status) {
            401, 403 => "Kunci API {$label} ditolak. Periksa kembali API key di file .env.",
            404 => "Model AI {$label} tidak ditemukan. Periksa pengaturan model di file .env.",
            429 => 'Kuota permintaan AI sudah habis untuk saat ini. Coba lagi nanti.',
            503 => 'Layanan AI sedang sibuk (permintaan melonjak). Coba lagi beberapa saat.',
            default => "Gagal membuat evaluasi dengan AI ({$label} menolak permintaan). Coba lagi nanti.",
        });
    }

    /**
     * Respons terpotong karena batas token.
     */
    public static function gagalTerpotong(): self
    {
        return self::gagal('Draf AI terpotong di tengah jalan (batas token tercapai). Coba lagi.');
    }

    /**
     * Respons 2xx tapi tanpa teks.
     */
    public static function gagalKosong(): self
    {
        return self::gagal('AI tidak mengembalikan teks evaluasi. Coba lagi.');
    }

    /**
     * Bentuk array yang dipakai `EvaluasiAiService::draftEvaluasi()`.
     *
     * @return array{sukses: bool, teks: string, pesan: string}
     */
    public function keArray(): array
    {
        return [
            'sukses' => $this->sukses,
            'teks' => $this->teks,
            'pesan' => $this->pesan,
        ];
    }
}
