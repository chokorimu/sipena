<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Services\Ai\AiManager;
use App\Services\Ai\AnthropicProvider;
use App\Services\Ai\GeminiProvider;
use App\Services\Ai\OpenAiProvider;
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

    /**
     * @param  array<string, mixed>  $tambahan
     */
    private function konfigurasi(array $tambahan = []): void
    {
        config(array_merge([
            'services.ai.driver' => 'openai',
            'services.ai.label' => null,
            'services.ai.base_url' => 'https://api.openai.com/v1',
            'services.ai.api_key' => 'kunci-uji',
            'services.ai.model' => 'gpt-4o-mini',
            'services.ai.timeout' => 60,
            'services.ai.max_tokens' => 4096,
            'services.ai.wajib_kunci' => true,
        ], $tambahan));
    }

    public function test_driver_openai_memakai_chat_completions(): void
    {
        $this->konfigurasi();

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
            && $request->hasHeader('Authorization', 'Bearer kunci-uji')
            && $request['model'] === 'gpt-4o-mini'
            && $request['max_tokens'] === 4096
            && str_contains($request['messages'][0]['content'], 'Matematika'));
    }

    public function test_base_url_bisa_diarahkan_ke_endpoint_sendiri(): void
    {
        $this->konfigurasi([
            'services.ai.base_url' => 'https://ai.contoh.id/v1/',
            'services.ai.model' => 'model-lokal',
        ]);

        Http::fake(['ai.contoh.id/*' => Http::response([
            'choices' => [['message' => ['content' => 'Baik.'], 'finish_reason' => 'stop']],
        ], 200)]);

        $hasil = $this->draft();

        $this->assertTrue($hasil['sukses']);

        // Trailing slash di base URL tidak boleh menghasilkan "//chat/completions".
        Http::assertSent(fn ($request): bool => $request->url() === 'https://ai.contoh.id/v1/chat/completions'
            && $request['model'] === 'model-lokal');
    }

    public function test_openai_tanpa_api_key_memberi_pesan_env(): void
    {
        $this->konfigurasi(['services.ai.api_key' => null]);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('AI_API_KEY', $hasil['pesan']);
        Http::assertNothingSent();
    }

    public function test_openai_terpotong_finish_reason_length(): void
    {
        $this->konfigurasi();

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

    public function test_endpoint_lokal_bisa_tanpa_api_key(): void
    {
        $this->konfigurasi([
            'services.ai.base_url' => 'http://localhost:11434/v1',
            'services.ai.model' => 'llama3.2',
            'services.ai.api_key' => null,
            'services.ai.wajib_kunci' => false,
        ]);

        Http::fake(['localhost:11434/*' => Http::response([
            'choices' => [['message' => ['content' => 'Baik.'], 'finish_reason' => 'stop']],
        ], 200)]);

        $hasil = $this->draft();

        $this->assertTrue($hasil['sukses']);
        Http::assertSent(fn ($request): bool => $request->url() === 'http://localhost:11434/v1/chat/completions'
            && ! $request->hasHeader('Authorization'));
    }

    public function test_driver_gemini_memakai_generate_content(): void
    {
        $this->konfigurasi([
            'services.ai.driver' => 'gemini',
            'services.ai.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'services.ai.model' => 'gemini-3.6-flash',
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

        Http::assertSent(fn ($request): bool => str_ends_with($request->url(), '/models/gemini-3.6-flash:generateContent')
            && $request->hasHeader('x-goog-api-key', 'kunci-uji')
            && str_contains($request['contents'][0]['parts'][0]['text'], 'Matematika'));
    }

    public function test_gemini_3_mengirim_thinking_config(): void
    {
        $this->konfigurasi([
            'services.ai.driver' => 'gemini',
            'services.ai.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'services.ai.model' => 'gemini-3.6-flash',
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
        $this->konfigurasi([
            'services.ai.driver' => 'gemini',
            'services.ai.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'services.ai.model' => 'gemini-2.5-flash',
        ]);

        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Baik.']]], 'finishReason' => 'STOP']],
        ], 200)]);

        $this->draft();

        Http::assertSent(fn ($request): bool => ! isset($request['generationConfig']['thinkingConfig']));
    }

    public function test_driver_anthropic_memakai_messages_api(): void
    {
        $this->konfigurasi([
            'services.ai.driver' => 'anthropic',
            'services.ai.base_url' => 'https://api.anthropic.com/v1',
            'services.ai.model' => 'model-uji',
        ]);

        Http::fake(['api.anthropic.com/*' => Http::response([
            'content' => [['type' => 'text', 'text' => 'Ananda tumbuh baik.']],
            'stop_reason' => 'end_turn',
        ], 200)]);

        $hasil = $this->draft();

        $this->assertTrue($hasil['sukses']);
        $this->assertSame('Ananda tumbuh baik.', $hasil['teks']);

        Http::assertSent(fn ($request): bool => str_ends_with($request->url(), '/v1/messages')
            && $request->hasHeader('x-api-key', 'kunci-uji')
            && $request->hasHeader('anthropic-version', '2023-06-01')
            && $request['model'] === 'model-uji'
            && str_contains($request['messages'][0]['content'], 'Matematika'));
    }

    public function test_anthropic_terpotong_max_tokens(): void
    {
        $this->konfigurasi([
            'services.ai.driver' => 'anthropic',
            'services.ai.base_url' => 'https://api.anthropic.com/v1',
            'services.ai.model' => 'model-uji',
        ]);

        Http::fake(['api.anthropic.com/*' => Http::response([
            'content' => [['type' => 'text', 'text' => 'Ananda telah']],
            'stop_reason' => 'max_tokens',
        ], 200)]);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('terpotong', $hasil['pesan']);
    }

    public function test_model_kosong_memberi_pesan_jelas(): void
    {
        $this->konfigurasi(['services.ai.model' => '']);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('AI_MODEL', $hasil['pesan']);
        Http::assertNothingSent();
    }

    public function test_driver_tidak_dikenal_memberi_pesan_jelas(): void
    {
        $this->konfigurasi(['services.ai.driver' => 'entah']);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString("Driver AI 'entah' tidak didukung", $hasil['pesan']);
        Http::assertNothingSent();
    }

    public function test_kunci_ditolak_memberi_pesan_jelas(): void
    {
        $this->konfigurasi([
            'services.ai.base_url' => 'https://api.groq.com/openai/v1',
            'services.ai.label' => 'Groq',
        ]);

        Http::fake(['api.groq.com/*' => Http::response(['error' => 'unauthorized'], 401)]);

        $hasil = $this->draft();

        $this->assertFalse($hasil['sukses']);
        $this->assertStringContainsString('ditolak', $hasil['pesan']);
    }

    public function test_label_dipakai_untuk_tooltip_dan_bisa_dikosongkan(): void
    {
        $this->konfigurasi(['services.ai.label' => 'Groq']);
        $this->assertSame('Groq', app(EvaluasiAiService::class)->namaProvider());

        $this->konfigurasi(['services.ai.label' => '']);
        $this->assertNull(app(EvaluasiAiService::class)->namaProvider());
    }

    public function test_manager_membangun_ketiga_driver(): void
    {
        $manager = app(AiManager::class);

        $this->konfigurasi(['services.ai.driver' => 'openai']);
        $this->assertInstanceOf(OpenAiProvider::class, $manager->provider());

        $this->konfigurasi(['services.ai.driver' => 'gemini']);
        $this->assertInstanceOf(GeminiProvider::class, $manager->provider());

        $this->konfigurasi(['services.ai.driver' => 'anthropic']);
        $this->assertInstanceOf(AnthropicProvider::class, $manager->provider());
    }

    public function test_draft_selalu_dipotong_di_akhir_kalimat(): void
    {
        $this->konfigurasi();

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
