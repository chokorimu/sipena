<?php

namespace App\Services\Ai;

/**
 * Kontrak satu penyedia AI (LLM) yang bisa membuat teks dari prompt.
 *
 * Implementasi baru cukup menambah kelas di namespace ini lalu daftarkan
 * drivernya di `AiManager` dan `config/services.php`.
 */
interface AiProvider
{
    /**
     * Kirim prompt dan kembalikan hasil (teks atau pesan gagal yang siap tampil).
     */
    public function buatTeks(string $prompt): AiResult;

    /**
     * Nama penyedia untuk ditampilkan di pesan error dan tooltip UI.
     */
    public function label(): string;
}
