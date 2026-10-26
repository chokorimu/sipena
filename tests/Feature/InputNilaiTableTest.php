<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InputNilaiTableTest extends TestCase
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

        return [$tahun, $kelas, $mapel, $siswa];
    }

    public function test_menampilkan_siswa_di_kelas_terpilih(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        Livewire::test('input-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->assertSee('Ahmad Fauzi')
            ->assertSee($siswa->nisn);
    }

    public function test_menyimpan_nilai_dan_menghitung_nilai_akhir(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        Livewire::test('input-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->set("nilai.{$siswa->id}.formatif", '80')
            ->set("nilai.{$siswa->id}.sumatif", '90')
            ->call('simpan')
            ->assertHasNoErrors();

        $nilai = Nilai::first();

        $this->assertNotNull($nilai);
        // 0,7 x 80 + 0,3 x 90 = 83
        $this->assertEquals(83.0, (float) $nilai->nilai_akhir);
        $this->assertSame('Lulus', $nilai->status_kelulusan);
    }

    public function test_status_tidak_lulus_jika_di_bawah_kkm(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        Livewire::test('input-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->set("nilai.{$siswa->id}.formatif", '60')
            ->set("nilai.{$siswa->id}.sumatif", '60')
            ->call('simpan')
            ->assertHasNoErrors();

        $nilai = Nilai::first();

        $this->assertNotNull($nilai);
        $this->assertEquals(60.0, (float) $nilai->nilai_akhir);
        $this->assertSame('Tidak Lulus', $nilai->status_kelulusan);
    }

    public function test_validasi_nilai_maksimal_100(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        Livewire::test('input-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->set("nilai.{$siswa->id}.formatif", '150')
            ->call('simpan')
            ->assertHasErrors("nilai.{$siswa->id}.formatif");

        $this->assertDatabaseCount('nilai', 0);
    }

    public function test_memuat_nilai_yang_sudah_tersimpan(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        Nilai::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 85,
            'sumatif' => 75,
        ]);

        Livewire::test('input-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->assertSet("nilai.{$siswa->id}.formatif", '85.00')
            ->assertSet("nilai.{$siswa->id}.sumatif", '75.00')
            ->assertSee('Lulus');
    }

    public function test_update_nilai_tidak_menambah_baris_baru(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        Nilai::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 70,
            'sumatif' => 70,
        ]);

        Livewire::test('input-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->set("nilai.{$siswa->id}.formatif", '90')
            ->set("nilai.{$siswa->id}.sumatif", '80')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('nilai', 1);
        $this->assertEquals(87.0, (float) Nilai::first()->nilai_akhir);
    }

    public function test_siswa_di_kelas_lain_tidak_tampil(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        $kelasLain = Kelas::factory()->create(['nama_kelas' => 'IX-B']);
        $siswaLain = Siswa::factory()->create(['nama' => 'Budi Santoso']);
        $siswaLain->kelas()->attach($kelasLain->id, ['tahun_id' => $tahun->id]);

        Livewire::test('input-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->assertSee('Ahmad Fauzi')
            ->assertDontSee('Budi Santoso');
    }
}
