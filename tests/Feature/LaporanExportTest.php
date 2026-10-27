<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_csv_berisi_statistik_dan_ringkasan(): void
    {
        $tahun = TahunAjaran::factory()->create(['tahun_ajaran' => '2024/2025', 'semester' => 'ganjil']);
        $kelas = Kelas::factory()->create(['nama_kelas' => 'VII-A']);
        $mapel = MataPelajaran::factory()->create(['nama_mapel' => 'Matematika']);

        $siswa = Siswa::factory()->create(['nama' => 'Ahmad Fauzi']);
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Nilai::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 80,
            'sumatif' => 90,
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get("/laporan/export?kelas={$kelas->id}&tahun={$tahun->id}");

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Laporan Akademik - Kelas VII-A', $content);
        $this->assertStringContainsString('Total Siswa', $content);
        $this->assertStringContainsString('Rata-rata Kelas', $content);
        $this->assertStringContainsString('Matematika', $content);
        // 0,7 x 80 + 0,3 x 90 = 83, format Indonesia: 83,0
        $this->assertStringContainsString('83,0', $content);
    }

    public function test_export_csv_tanpa_parameter_memakai_default(): void
    {
        $tahun = TahunAjaran::factory()->create();
        $kelas = Kelas::factory()->create(['nama_kelas' => 'VII-A']);
        $mapel = MataPelajaran::factory()->create(['nama_mapel' => 'Matematika']);

        $siswa = Siswa::factory()->create(['nama' => 'Ahmad Fauzi']);
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Nilai::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 80,
            'sumatif' => 90,
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get('/laporan/export');

        $response->assertOk();

        $content = $response->streamedContent();

        $this->assertStringContainsString('Matematika', $content);
        $this->assertStringContainsString('83,0', $content);
    }

    public function test_export_csv_menolak_parameter_tidak_valid(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/laporan/export?kelas=999999')
            ->assertSessionHasErrors('kelas');
    }

    public function test_tamu_tidak_bisa_export(): void
    {
        $this->get('/laporan/export')->assertRedirect('/login');
    }
}
