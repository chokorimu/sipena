<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Google Gemini (`generateContent`).
 *
 * Catatan: model seri 3.x menghitung thinking token ke `maxOutputTokens`, jadi
 * `maxOutputTokens` harus lega dan `thinkingLevel` dibuat rendah. Model lama
 * (2.5 / 1.5) memakai `thinkingBudget`, bukan `thinkingLevel`, sehingga
 * `thinkingConfig` hanya dikirim untuk seri 3.x.
 */
final class GeminiProvider implements AiProvider
{
    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $model,
        private readonly string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta',
        private readonly int $timeout = 60,
        private readonly int $maxTokens = 4096,
    ) {}

    public function label(): string
    {
        return 'Gemini';
    }

    public function buatTeks(string $prompt): AiResult
    {
        if ($this->apiKey === null || $this->apiKey === '') {
            return AiResult::gagalKunci($this->label(), 'AI_API_KEY');
        }

        $url = rtrim($this->baseUrl, '/')."/models/{$this->model}:generateContent";

        try {
            $response = Http::withHeaders(['x-goog-api-key' => $this->apiKey])
                ->timeout($this->timeout)
                ->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $prompt]],
                        ],
                    ],
                    'generationConfig' => $this->generationConfig(),
                ]);
        } catch (Throwable $e) {
            Log::warning("Gemini evaluasi gagal ({$this->model}): ".$e->getMessage());

            return AiResult::gagalKoneksi($this->label());
        }

        if ($response->failed()) {
            Log::warning('Gemini evaluasi HTTP '.$response->status().': '.mb_substr($response->body(), 0, 500));

            return AiResult::gagalStatus($response->status(), $this->label());
        }

        $json = $response->json();

        if (data_get($json, 'candidates.0.finishReason') === 'MAX_TOKENS') {
            Log::warning("Gemini evaluasi terpotong (MAX_TOKENS) pada model {$this->model}");

            return AiResult::gagalTerpotong();
        }

        $teks = trim((string) data_get($json, 'candidates.0.content.parts.0.text', ''));

        return $teks === '' ? AiResult::gagalKosong() : AiResult::ok($teks);
    }

    /**
     * @return array<string, mixed>
     */
    private function generationConfig(): array
    {
        $config = ['maxOutputTokens' => $this->maxTokens];

        if (str_starts_with($this->model, 'gemini-3')) {
            $config['thinkingConfig'] = ['thinkingLevel' => 'low'];
        }

        return $config;
    }
}
