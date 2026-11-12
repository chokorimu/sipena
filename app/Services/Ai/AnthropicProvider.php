<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Anthropic Claude Messages API (`/v1/messages`).
 *
 * Formatnya berbeda dari chat completions: prompt masuk ke `messages[].content`,
 * hasil ada di `content[].text`, dan penanda terpotong berupa `stop_reason`
 * bernilai `max_tokens`.
 */
final class AnthropicProvider implements AiProvider
{
    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $model,
        private readonly string $baseUrl = 'https://api.anthropic.com/v1',
        private readonly string $envKey = 'ANTHROPIC_API_KEY',
        private readonly int $timeout = 60,
        private readonly int $maxTokens = 4096,
    ) {}

    public function label(): string
    {
        return 'Claude';
    }

    public function buatTeks(string $prompt): AiResult
    {
        if ($this->apiKey === null || $this->apiKey === '') {
            return AiResult::gagalKunci($this->label(), $this->envKey);
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
            ])
                ->timeout($this->timeout)
                ->post(rtrim($this->baseUrl, '/').'/messages', [
                    'model' => $this->model,
                    'max_tokens' => $this->maxTokens,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);
        } catch (Throwable $e) {
            Log::warning("Claude evaluasi gagal ({$this->model}): ".$e->getMessage());

            return AiResult::gagalKoneksi($this->label());
        }

        if ($response->failed()) {
            Log::warning('Claude evaluasi HTTP '.$response->status().': '.mb_substr($response->body(), 0, 500));

            return AiResult::gagalStatus($response->status(), $this->label());
        }

        $json = $response->json();

        if (data_get($json, 'stop_reason') === 'max_tokens') {
            Log::warning("Claude evaluasi terpotong (max_tokens) pada model {$this->model}");

            return AiResult::gagalTerpotong();
        }

        $teks = trim((string) data_get($json, 'content.0.text', ''));

        return $teks === '' ? AiResult::gagalKosong() : AiResult::ok($teks);
    }
}
