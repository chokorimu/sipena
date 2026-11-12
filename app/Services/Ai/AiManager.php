<?php

namespace App\Services\Ai;

use InvalidArgumentException;

/**
 * Memilih provider AI aktif berdasarkan `config('services.ai.*')`.
 *
 * Provider dipilih lewat `AI_PROVIDER` di .env. Menambah penyedia baru yang
 * formatnya kompatibel chat completions cukup dilakukan di config — tidak perlu
 * kelas baru.
 */
final class AiManager
{
    /**
     * Nama provider aktif (key di `services.ai.providers`).
     */
    public function nama(): string
    {
        return (string) config('services.ai.default', 'gemini');
    }

    /**
     * Label provider aktif untuk UI (jatuh ke "AI" kalau konfigurasi salah).
     */
    public function label(): string
    {
        try {
            return $this->provider()->label();
        } catch (InvalidArgumentException) {
            return 'AI';
        }
    }

    /**
     * Bangun instance provider sesuai konfigurasi.
     *
     * @throws InvalidArgumentException kalau provider/driver/model tidak dikenal
     */
    public function provider(?string $nama = null): AiProvider
    {
        $nama ??= $this->nama();
        $konfigurasi = config("services.ai.providers.{$nama}");

        if (! is_array($konfigurasi)) {
            throw new InvalidArgumentException(
                "Provider AI '{$nama}' tidak dikenal. Periksa AI_PROVIDER di file .env."
            );
        }

        $driver = (string) ($konfigurasi['driver'] ?? $nama);
        $label = (string) ($konfigurasi['label'] ?? ucfirst($nama));
        $model = (string) ($konfigurasi['model'] ?? '');
        $apiKey = $this->teksAtauNull($konfigurasi['api_key'] ?? null);
        $timeout = (int) config('services.ai.timeout', 60);
        $maxTokens = (int) config('services.ai.max_tokens', 4096);

        if ($model === '') {
            throw new InvalidArgumentException(
                "Model AI untuk provider '{$nama}' belum dikonfigurasi. Tambahkan ".strtoupper($nama).'_MODEL di file .env.'
            );
        }

        return match ($driver) {
            'gemini' => new GeminiProvider(
                apiKey: $apiKey,
                model: $model,
                timeout: $timeout,
                maxTokens: $maxTokens,
            ),
            'openai' => new OpenAiProvider(
                nama: $label,
                apiKey: $apiKey,
                model: $model,
                baseUrl: (string) ($konfigurasi['base_url'] ?? 'https://api.openai.com/v1'),
                envKey: (string) ($konfigurasi['env_key'] ?? 'API_KEY'),
                timeout: $timeout,
                maxTokens: $maxTokens,
                headers: (array) ($konfigurasi['headers'] ?? []),
                wajibKunci: (bool) ($konfigurasi['wajib_kunci'] ?? true),
            ),
            'anthropic' => new AnthropicProvider(
                apiKey: $apiKey,
                model: $model,
                baseUrl: (string) ($konfigurasi['base_url'] ?? 'https://api.anthropic.com/v1'),
                envKey: (string) ($konfigurasi['env_key'] ?? 'ANTHROPIC_API_KEY'),
                timeout: $timeout,
                maxTokens: $maxTokens,
            ),
            default => throw new InvalidArgumentException(
                "Driver AI '{$driver}' tidak didukung. Periksa AI_PROVIDER di file .env."
            ),
        };
    }

    private function teksAtauNull(mixed $nilai): ?string
    {
        return is_string($nilai) && $nilai !== '' ? $nilai : null;
    }
}
