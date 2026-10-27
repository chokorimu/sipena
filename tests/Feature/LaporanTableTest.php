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

class LaporanTableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: TahunAjaran, 1: Kelas, 2: MataPelajaran}
     */
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

    public function test_statistik_kelas_dihitung_dari_data(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        // rata-rata 100 → tuntas
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);
        // rata-rata 50 → belum tuntas
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Budi Santoso', 50, 50);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->assertSee('Total Siswa')
            // rata-rata kelas (100 + 50) / 2 = 75.0
            ->assertSee('75.0')
            ->assertSee('Tuntas')
            ->assertSee('Belum Tuntas');
    }

    public function test_ringkasan_per_mata_pelajaran(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Budi Santoso', 50, 50);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->assertSee('Ringkasan Nilai per Mata Pelajaran')
            ->assertSee('Matematika')
            ->assertSee('Rata-rata Kelas');
    }

    public function test_distribusi_predikat(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        // nilai akhir 100 → predikat A
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);
        // nilai akhir 50 → predikat E
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Budi Santoso', 50, 50);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->assertSee('Distribusi Predikat')
            ->assertSee('Predikat A')
            ->assertSee('Predikat E');
    }

    public function test_catatan_dibuat_dari_data(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Budi Santoso', 50, 50);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->assertSee('Catatan')
            ->assertSee('Remedial Diperlukan')
            // 1 dari 2 siswa tuntas → 50% < 75% → peringatan
            ->assertSee('Pencapaian KKM Perlu Perhatian');
    }

    public function test_jenis_laporan_lain_tampil_pesan_belum_tersedia(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->assertSee('belum tersedia')
            ->assertDontSee('Ringkasan Nilai per Mata Pelajaran');
    }

    public function test_kelas_lain_tidak_tercampur(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        // kelas terpilih: rata-rata 100
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);

        $kelasLain = Kelas::factory()->create(['nama_kelas' => 'IX-B']);
        $this->tambahSiswaDenganNilai($kelasLain, $tahun, $mapel, 'Dewi Lestari', 50, 50);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            // rata-rata kelas terpilih 100; kalau kelas lain tercampur jadi 75
            ->assertSee('100.0')
            ->assertDontSee('75.0');
    }

    public function test_tanpa_data_tampil_pesan_kosong(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->assertSee('Belum Ada Data')
            ->assertSee('Belum ada nilai yang tercatat');
    }
}
