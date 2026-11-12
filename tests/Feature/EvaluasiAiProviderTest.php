<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Services\EvaluasiAiService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EvaluasiAiProviderTest extends TestCase
{
    /**
     * Nilai contoh tanpa menyentuh database.
     *
     * @return Collection<int, Nilai>
     */
    private function nilaiContoh(): Collection
    {
        $nilai = new Nilai([
            'formatif' => 80,
            'sumatif' => 90,
            'nilai_akhir' => 83,
        ]);

        $nilai->setRelation('mataPelajaran', new MataPelajaran(['nama_mapel' => 'Matematika']));

        return collect([$nilai]);
    }

    /**
     * @return array{sukses: bool, teks: string, pesan: string}
     */
    private function draft(): array
    {
        return app(EvaluasiAiService::class)->draftEvaluasi(
            $this->nilaiContoh(),
            83.0,
            'Tuntas',
            'B',
            'VII-A',
            '2024/2025 ganjil',
        );
    }

    public function test_provider_default_adalah_gemini(): void
    {
        config([
            'services.ai.default' => 'gemini',
            'services.ai.providers.gemini.api_key' => 'kunci-uji',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Ananda berkembang baik.']]], 'finishReason' => 'STOP'],
                ],
            ], 200),
        ]);

        $hasil = $this->draft();

        $this->assertTrue($hasil['sukses']);
        $this->assertSame('Ananda berkembang baik.', $hasil['teks']);

        Http::assertSent(fn ($request): bool => str_contains($request->url(), 'generativelanguage.googleapis.com')
            && $request->hasHeader('x-goog-api-key', 'kunci-uji')
            && str_contains($request['contents'][0]['parts'][0]['text'], 'Matematika'));
    }

    public function test_gemini_3_mengirim_thinking_config(): void
    {
        config([
            'services.ai.default' => 'gemini',
            'services.ai.providers.gemini.api_key' => 'kunci-uji',
            'services.ai.providers.gemini.model' => 'gemini-3.6-flash',
        ]);

        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Baik.']]], 'finishReason' => 'STOP']],
        ], 200)]);

        $this->draft();

        Http::assertSent(fn ($request): bool => ($request['generationConfig']['thinkingConfig']['thinkingLevel'] ?? null) === 'low'
            && $request['generationConfig']['maxOutputTokens'] === 4096);
    }

    public function test_gemini_25_tidak_mengirim_thinking_config(): void
    {
        // Model 2.5 memakai thinkingBudget, bukan thinkingLevel — mengirim
        // thinkingConfig akan ditolak API dengan 400.
        config([
            'services.ai.default' => 'gemini',
            'services.ai.providers.gemini.api_key' => 'kunci-uji',
            'services.ai.providers.gemini.model' => 'gemini-2.5-flash',
        ]);

        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Baik.']]], 'finishReason' => 'STOP']],
        ], 200)]);

        $this->draft();

        Http::assertSent(fn ($request): bool => ! isset($request['generationConfig']['thinkingConfig']));
    }

    public function test_openai_memakai_chat_completions(): void
    {
        config([
            'services.ai.default' => 'openai',
            'services.ai.providers.openai.api_key' => 'sk-uji',
        ]);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Ananda menunjukkan kemajuan.']], 'finish_reason' => 'stop',
                ],
            ], 200),
        ]);

        $hasil = $this->draft();

        $this->assertTrue($hasil['sukses']);
        $this->assertSame('Ananda menunjukkan kemajuan.', $hasil['teks']);

        Http::assertSent(fn ($request): bool => str_ends_with($request->url(), '/chat/completions')
            && $request->hasHeader('Authorization', 'Bearer sk-uji')
            && $request['model'] === 'gpt-4o-mini'
            && $request['max_tokens'] === 4096
            && str_contains($request['messages'][0]['content'], 'Matematika'));
    }

    public function test_openai_tanpa_api_key_memberi_pesan_env(): void
    {
        config([
            'services.ai.default' => 'openai',
            'services.ai.providers.openai.api_key' => null,
        ]);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('OPENAI_API_KEY', $hasil['pesan']);
        Http::assertNothingSent();
    }

    public function test_openai_terpotong_finish_reason_length(): void
    {
        config([
            'services.ai.default' => 'openai',
            'services.ai.providers.openai.api_key' => 'sk-uji',
        ]);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Ananda telah menunjukkan usaha'], 'finish_reason' => 'length'],
                ],
            ], 200),
        ]);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertSame('', $hasil['teks']);
        $this->assertStringContainsString('terpotong', $hasil['pesan']);
    }

    public function test_openrouter_mengirim_header_tambahan(): void
    {
        config([
            'services.ai.default' => 'openrouter',
            'services.ai.providers.openrouter.api_key' => 'or-uji',
        ]);

        Http::fake(['openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => 'Baik.'], 'finish_reason' => 'stop']],
        ], 200)]);

        $hasil = $this->draft();

        $this->assertTrue($hasil['sukses']);

        Http::assertSent(fn ($request): bool => $request->hasHeader('Authorization', 'Bearer or-uji')
            && $request->hasHeader('HTTP-Referer')
            && $request->hasHeader('X-Title'));
    }

    public function test_ollama_jalan_tanpa_api_key(): void
    {
        config([
            'services.ai.default' => 'ollama',
            'services.ai.providers.ollama.api_key' => null,
        ]);

        Http::fake(['localhost:11434/*' => Http::response([
            'choices' => [['message' => ['content' => 'Baik.'], 'finish_reason' => 'stop']],
        ], 200)]);

        $hasil = $this->draft();

        $this->assertTrue($hasil['sukses']);
        Http::assertSent(fn ($request): bool => str_contains($request->url(), 'localhost:11434/v1/chat/completions')
            && ! $request->hasHeader('Authorization'));
    }

    public function test_anthropic_memakai_messages_api(): void
    {
        config([
            'services.ai.default' => 'anthropic',
            'services.ai.providers.anthropic.api_key' => 'sk-ant-uji',
            'services.ai.providers.anthropic.model' => 'model-uji',
        ]);

        Http::fake(['api.anthropic.com/*' => Http::response([
            'content' => [['type' => 'text', 'text' => 'Ananda tumbuh baik.']],
            'stop_reason' => 'end_turn',
        ], 200)]);

        $hasil = $this->draft();

        $this->assertTrue($hasil['sukses']);
        $this->assertSame('Ananda tumbuh baik.', $hasil['teks']);

        Http::assertSent(fn ($request): bool => str_ends_with($request->url(), '/v1/messages')
            && $request->hasHeader('x-api-key', 'sk-ant-uji')
            && $request->hasHeader('anthropic-version', '2023-06-01')
            && $request['model'] === 'model-uji'
            && str_contains($request['messages'][0]['content'], 'Matematika'));
    }

    public function test_anthropic_terpotong_max_tokens(): void
    {
        config([
            'services.ai.default' => 'anthropic',
            'services.ai.providers.anthropic.api_key' => 'sk-ant-uji',
            'services.ai.providers.anthropic.model' => 'model-uji',
        ]);

        Http::fake(['api.anthropic.com/*' => Http::response([
            'content' => [['type' => 'text', 'text' => 'Ananda telah']],
            'stop_reason' => 'max_tokens',
        ], 200)]);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('terpotong', $hasil['pesan']);
    }

    public function test_anthropic_tanpa_model_memberi_pesan_jelas(): void
    {
        config([
            'services.ai.default' => 'anthropic',
            'services.ai.providers.anthropic.api_key' => 'sk-ant-uji',
            'services.ai.providers.anthropic.model' => '',
        ]);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('ANTHROPIC_MODEL', $hasil['pesan']);
        Http::assertNothingSent();
    }

    public function test_provider_tidak_dikenal_memberi_pesan_jelas(): void
    {
        config(['services.ai.default' => 'entah']);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString("Provider AI 'entah' tidak dikenal", $hasil['pesan']);
        Http::assertNothingSent();
    }

    public function test_kunci_ditolak_memberi_pesan_jelas(): void
    {
        config([
            'services.ai.default' => 'groq',
            'services.ai.providers.groq.api_key' => 'kunci-salah',
        ]);

        Http::fake(['api.groq.com/*' => Http::response(['error' => 'unauthorized'], 401)]);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('ditolak', $hasil['pesan']);
    }

    public function test_label_provider_dipakai_untuk_tooltip(): void
    {
        config([
            'services.ai.default' => 'groq',
            'services.ai.providers.groq.api_key' => 'kunci-uji',
        ]);

        $this->assertSame('Groq', app(EvaluasiAiService::class)->namaProvider());

        config(['services.ai.default' => 'entah']);
        $this->assertSame('AI', app(EvaluasiAiService::class)->namaProvider());
    }

    public function test_draft_selalu_dipotong_di_akhir_kalimat(): void
    {
        config([
            'services.ai.default' => 'openai',
            'services.ai.providers.openai.api_key' => 'sk-uji',
        ]);

        Http::fake(['api.openai.com/*' => Http::response([
            'choices' => [
                ['message' => ['content' => str_repeat('Ananda menunjukkan perkembangan yang baik. ', 120)], 'finish_reason' => 'stop'],
            ],
        ], 200)]);

        $hasil = $this->draft();

        $this->assertTrue($hasil['sukses']);
        $this->assertLessThanOrEqual(1800, mb_strlen($hasil['teks']));
        $this->assertStringEndsWith('.', $hasil['teks']);
    }
}
