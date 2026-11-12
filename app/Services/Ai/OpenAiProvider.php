<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Driver untuk penyedia yang memakai format chat completions ala OpenAI.
 *
 * Satu kelas ini melayani banyak penyedia (OpenAI, OpenRouter, Groq, DeepSeek,
 * Mistral, xAI, Ollama lokal, dan lainnya) — cukup beda `base_url`, `model`,
 * dan `api_key` di `config/services.php`.
 */
final class OpenAiProvider implements AiProvider
{
    /**
     * @param  array<string, string>  $headers  Header tambahan (mis. HTTP-Referer untuk OpenRouter).
     */
    public function __construct(
        private readonly string $nama,
        private readonly ?string $apiKey,
        private readonly string $model,
        private readonly string $baseUrl,
        private readonly string $envKey = 'API_KEY',
        private readonly int $timeout = 60,
        private readonly int $maxTokens = 4096,
        private readonly array $headers = [],
        private readonly bool $wajibKunci = true,
    ) {}

    public function label(): string
    {
        return $this->nama;
    }

    public function buatTeks(string $prompt): AiResult
    {
        if ($this->wajibKunci && ($this->apiKey === null || $this->apiKey === '')) {
            return AiResult::gagalKunci($this->label(), $this->envKey);
        }

        $headers = $this->headers;

        if ($this->apiKey !== null && $this->apiKey !== '') {
            $headers['Authorization'] = 'Bearer '.$this->apiKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->post(rtrim($this->baseUrl, '/').'/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => $this->maxTokens,
                ]);
        } catch (Throwable $e) {
            Log::warning("{$this->nama} evaluasi gagal ({$this->model}): ".$e->getMessage());

            return AiResult::gagalKoneksi($this->label());
        }

        if ($response->failed()) {
            Log::warning("{$this->nama} evaluasi HTTP ".$response->status().': '.mb_substr($response->body(), 0, 500));

            return AiResult::gagalStatus($response->status(), $this->label());
        }

        $json = $response->json();

        // `length` = terpotong karena batas token, sama seperti MAX_TOKENS di Gemini.
        if (data_get($json, 'choices.0.finish_reason') === 'length') {
            Log::warning("{$this->nama} evaluasi terpotong (finish_reason: length) pada model {$this->model}");

            return AiResult::gagalTerpotong();
        }

        $teks = trim((string) data_get($json, 'choices.0.message.content', ''));

        return $teks === '' ? AiResult::gagalKosong() : AiResult::ok($teks);
    }
}
