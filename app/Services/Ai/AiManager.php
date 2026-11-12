<?php

namespace App\Services\Ai;

use InvalidArgumentException;

/**
 * Membangun provider AI dari satu blok konfigurasi `services.ai.*`.
 *
 * Driver menentukan format API-nya, sedangkan base URL, model, dan API key
 * diatur sendiri lewat .env (AI_DRIVER, AI_BASE_URL, AI_MODEL, AI_API_KEY).
 */
final class AiManager
{
    /**
     * Driver aktif (format API): openai | gemini | anthropic.
     */
    public function driver(): string
    {
        return (string) config('services.ai.driver', 'openai');
    }

    /**
     * Label untuk tooltip UI, atau null kalau tidak diisi (tooltip tanpa nama).
     */
    public function label(): ?string
    {
        $label = config('services.ai.label');

        return is_string($label) && trim($label) !== '' ? trim($label) : null;
    }

    /**
     * Bangun instance provider sesuai konfigurasi.
     *
     * @throws InvalidArgumentException kalau driver tidak dikenal atau model kosong
     */
    public function provider(): AiProvider
    {
        $driver = $this->driver();
        $model = trim((string) config('services.ai.model'));

        if ($model === '') {
            throw new InvalidArgumentException('Model AI belum dikonfigurasi. Tambahkan AI_MODEL di file .env.');
        }

        $apiKey = $this->teksAtauNull(config('services.ai.api_key'));
        $baseUrl = rtrim((string) config('services.ai.base_url'), '/');
        $timeout = (int) config('services.ai.timeout', 60);
        $maxTokens = (int) config('services.ai.max_tokens', 4096);
        $wajibKunci = (bool) config('services.ai.wajib_kunci', true);

        if ($baseUrl === '') {
            throw new InvalidArgumentException('Base URL AI belum dikonfigurasi. Tambahkan AI_BASE_URL di file .env.');
        }

        return match ($driver) {
            'gemini' => new GeminiProvider(
                apiKey: $apiKey,
                model: $model,
                baseUrl: $baseUrl,
                timeout: $timeout,
                maxTokens: $maxTokens,
            ),
            'openai' => new OpenAiProvider(
                nama: $this->label() ?? 'AI',
                apiKey: $apiKey,
                model: $model,
                baseUrl: $baseUrl,
                timeout: $timeout,
                maxTokens: $maxTokens,
                wajibKunci: $wajibKunci,
            ),
            'anthropic' => new AnthropicProvider(
                apiKey: $apiKey,
                model: $model,
                baseUrl: $baseUrl,
                timeout: $timeout,
                maxTokens: $maxTokens,
                wajibKunci: $wajibKunci,
            ),
            default => throw new InvalidArgumentException(
                "Driver AI '{$driver}' tidak didukung. Isi AI_DRIVER dengan openai, gemini, atau anthropic."
            ),
        };
    }

    private function teksAtauNull(mixed $nilai): ?string
    {
        return is_string($nilai) && $nilai !== '' ? $nilai : null;
    }
}
