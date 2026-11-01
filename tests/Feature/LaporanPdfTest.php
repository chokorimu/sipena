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

class LaporanPdfTest extends TestCase
{
    use RefreshDatabase;

    private function siapkanData(): array
    {
        $tahun = TahunAjaran::factory()->create(['tahun_ajaran' => '2024/2025', 'semester' => 'ganjil']);
        $kelas = Kelas::factory()->create(['nama_kelas' => 'VII-A']);
        $mapel = MataPelajaran::factory()->create(['nama_mapel' => 'Matematika']);

        return [$tahun, $kelas, $mapel];
    }

    private function tambahSiswaDenganNilai(Kelas $kelas, TahunAjaran $tahun, MataPelajaran $mapel, string $nama, int $formatif, int $sumatif): Siswa
    {
        $siswa = Siswa::factory()->create(['nama' => $nama]);
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Nilai::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => $formatif,
            'sumatif' => $sumatif,
        ]);

        return $siswa;
    }

    public function test_pdf_rekap_kelas_berhasil_diunduh(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        $response = $this->actingAs(User::factory()->create())
            ->get("/laporan/pdf?kelas={$kelas->id}&tahun={$tahun->id}");

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_pdf_rapor_siswa_berhasil_diunduh(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        $response = $this->actingAs(User::factory()->create())
            ->get("/laporan/pdf?jenis=rapor-siswa&kelas={$kelas->id}&tahun={$tahun->id}&siswa={$siswa->id}");

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_pdf_rapor_tanpa_param_siswa_pakai_siswa_pertama(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        $response = $this->actingAs(User::factory()->create())
            ->get("/laporan/pdf?jenis=rapor-siswa&kelas={$kelas->id}&tahun={$tahun->id}");

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_pdf_analisis_nilai_berhasil_diunduh(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Budi Santoso', 50, 50);

        $response = $this->actingAs(User::factory()->create())
            ->get("/laporan/pdf?jenis=analisis-nilai&kelas={$kelas->id}&tahun={$tahun->id}&mapel={$mapel->id}");

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_pdf_tanpa_parameter_memakai_default(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        $response = $this->actingAs(User::factory()->create())
            ->get('/laporan/pdf');

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_pdf_menolak_jenis_tidak_valid(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->actingAs(User::factory()->create())
            ->get('/laporan/pdf?jenis=rahasia')
            ->assertSessionHasErrors('jenis');
    }

    public function test_pdf_menolak_siswa_dari_kelas_lain(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        $kelasLain = Kelas::factory()->create(['nama_kelas' => 'IX-B']);
        $siswaLain = $this->tambahSiswaDenganNilai($kelasLain, $tahun, $mapel, 'Dewi Lestari', 80, 90);

        $this->actingAs(User::factory()->create())
            ->get("/laporan/pdf?jenis=rapor-siswa&kelas={$kelas->id}&tahun={$tahun->id}&siswa={$siswaLain->id}")
            ->assertNotFound();
    }

    public function test_pdf_menolak_kelas_tidak_valid(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/laporan/pdf?kelas=999999')
            ->assertSessionHasErrors('kelas');
    }

    public function test_tamu_tidak_bisa_unduh_pdf(): void
    {
        $this->get('/laporan/pdf')->assertRedirect('/login');
    }
}
