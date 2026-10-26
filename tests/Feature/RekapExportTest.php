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

class RekapExportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: TahunAjaran, 1: Kelas, 2: MataPelajaran, 3: Siswa}
     */
    private function siapkanData(): array
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

        return [$tahun, $kelas, $mapel, $siswa];
    }

    public function test_export_csv_berisi_rekap_nilai(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        $response = $this->actingAs(User::factory()->create())
            ->get("/rekap-nilai/export?kelas={$kelas->id}&mapel={$mapel->id}&tahun={$tahun->id}");

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Peringkat', $content);
        $this->assertStringContainsString('Nama Siswa', $content);
        $this->assertStringContainsString('Nilai Akhir', $content);
        $this->assertStringContainsString('Ahmad Fauzi', $content);
        $this->assertStringContainsString($siswa->nisn, $content);
        // 0,7 x 80 + 0,3 x 90 = 83, format Indonesia: 83,00
        $this->assertStringContainsString('83,00', $content);
        $this->assertStringContainsString('Lulus', $content);
        $this->assertStringContainsString('A', $content);
    }

    public function test_export_csv_tanpa_parameter_memakai_default(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        $response = $this->actingAs(User::factory()->create())
            ->get('/rekap-nilai/export');

        $response->assertOk();

        $content = $response->streamedContent();

        $this->assertStringContainsString('Ahmad Fauzi', $content);
        $this->assertStringContainsString('83,00', $content);
    }

    public function test_export_csv_menolak_parameter_tidak_valid(): void
    {
        $this->siapkanData();

        $this->actingAs(User::factory()->create())
            ->get('/rekap-nilai/export?kelas=999999')
            ->assertSessionHasErrors('kelas');
    }

    public function test_tamu_tidak_bisa_export(): void
    {
        $this->get('/rekap-nilai/export')->assertRedirect('/login');
    }
}
