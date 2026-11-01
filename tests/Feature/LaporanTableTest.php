<?php

namespace Tests\Feature;

use App\Models\Evaluasi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
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

    public function test_rapor_siswa_tampil_nilai_per_mapel(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->set('siswaId', (string) $siswa->id)
            ->assertSee('Siti Nurhaliza')
            ->assertSee('Matematika')
            // 0,7 x 80 + 0,3 x 90 = 83,0
            ->assertSee('83.0')
            ->assertSee('Tuntas');
    }

    public function test_rapor_siswa_belum_tuntas_kalau_di_bawah_kkm(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Budi Santoso', 50, 50);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->set('siswaId', (string) $siswa->id)
            ->assertSee('Belum Tuntas')
            // rata-rata 50 → predikat E
            ->assertSee('Predikat E');
    }

    public function test_rapor_siswa_tanpa_pilihan_tampil_pesan(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->assertSee('Pilih siswa untuk melihat rapor');
    }

    public function test_ganti_kelas_reset_pilihan_siswa(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);
        $kelasLain = Kelas::factory()->create(['nama_kelas' => 'IX-B']);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->set('siswaId', (string) $siswa->id)
            ->assertSet('siswaId', (string) $siswa->id)
            ->set('kelasId', (string) $kelasLain->id)
            ->assertSet('siswaId', '');
    }

    public function test_analisis_nilai_tampil_perbandingan_dan_sebaran(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Budi Santoso', 50, 50);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'analisis-nilai')
            ->assertSee('Perbandingan Rata-rata per Mata Pelajaran')
            ->assertSee('Sebaran Nilai')
            ->assertSee('Siswa Perlu Perhatian');
    }

    public function test_siswa_perhatian_hanya_di_bawah_kkm(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 100, 100);
        $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Budi Santoso', 50, 50);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'analisis-nilai')
            // Budi rata-rata 50 < KKM → masuk daftar perhatian
            ->assertSee('Budi Santoso')
            // Siti rata-rata 100 ≥ KKM → tidak masuk daftar perhatian
            ->assertDontSee('Siti Nurhaliza');
    }

    public function test_simpan_evaluasi_siswa(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->set('siswaId', (string) $siswa->id)
            ->set('evaluasiTeks', 'Ananda menunjukkan perkembangan baik dalam matematika.')
            ->call('simpanEvaluasi')
            ->assertHasNoErrors()
            ->assertSee('berhasil disimpan')
            ->assertSee('Ananda menunjukkan perkembangan baik dalam matematika.');

        $this->assertDatabaseHas('evaluasi', [
            'siswa_id' => $siswa->id,
            'tahun_id' => $tahun->id,
            'catatan' => 'Ananda menunjukkan perkembangan baik dalam matematika.',
        ]);
    }

    public function test_simpan_evaluasi_dua_kali_tidak_duplikat(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->set('siswaId', (string) $siswa->id)
            ->set('evaluasiTeks', 'Evaluasi versi satu.')
            ->call('simpanEvaluasi')
            ->set('evaluasiTeks', 'Evaluasi versi dua.')
            ->call('simpanEvaluasi');

        $this->assertDatabaseCount('evaluasi', 1);
        $this->assertDatabaseHas('evaluasi', [
            'siswa_id' => $siswa->id,
            'tahun_id' => $tahun->id,
            'catatan' => 'Evaluasi versi dua.',
        ]);
    }

    public function test_kosongkan_evaluasi_menghapus_catatan(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        Evaluasi::create([
            'siswa_id' => $siswa->id,
            'tahun_id' => $tahun->id,
            'catatan' => 'Evaluasi lama.',
        ]);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->set('siswaId', (string) $siswa->id)
            ->set('evaluasiTeks', '')
            ->call('simpanEvaluasi')
            ->assertSee('dikosongkan');

        $this->assertDatabaseMissing('evaluasi', [
            'siswa_id' => $siswa->id,
            'tahun_id' => $tahun->id,
        ]);
    }

    public function test_evaluasi_melebihi_2000_karakter_ditolak(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->set('siswaId', (string) $siswa->id)
            ->set('evaluasiTeks', str_repeat('a', 2001))
            ->call('simpanEvaluasi')
            ->assertHasErrors('evaluasiTeks');
    }

    public function test_ganti_siswa_memuat_evaluasi_tersimpan(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siti = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);
        $budi = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Budi Santoso', 70, 80);

        Evaluasi::create([
            'siswa_id' => $siti->id,
            'tahun_id' => $tahun->id,
            'catatan' => 'Evaluasi Siti sangat baik.',
        ]);

        Evaluasi::create([
            'siswa_id' => $budi->id,
            'tahun_id' => $tahun->id,
            'catatan' => 'Evaluasi Budi perlu remedial.',
        ]);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->set('siswaId', (string) $siti->id)
            ->assertSee('Evaluasi Siti sangat baik.')
            ->set('siswaId', (string) $budi->id)
            ->assertSee('Evaluasi Budi perlu remedial.')
            ->assertDontSee('Evaluasi Siti sangat baik.');
    }

    public function test_tombol_ai_evaluasi_placeholder(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        Livewire::test('laporan-table')
            ->set('kelasId', (string) $kelas->id)
            ->set('tahunId', (string) $tahun->id)
            ->set('jenis', 'rapor-siswa')
            ->set('siswaId', (string) $siswa->id)
            ->assertSee('Catatan Evaluasi')
            ->assertSee('Buat Evaluasi dengan AI')
            ->assertSee('Simpan Evaluasi');
    }

    public function test_halaman_laporan_url_siswa_memuat_evaluasi(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswa = $this->tambahSiswaDenganNilai($kelas, $tahun, $mapel, 'Siti Nurhaliza', 80, 90);

        Evaluasi::create([
            'siswa_id' => $siswa->id,
            'tahun_id' => $tahun->id,
            'catatan' => 'Evaluasi dari URL bookmark.',
        ]);

        $this->actingAs(User::factory()->create())
            ->get("/laporan?jenis=rapor-siswa&kelas={$kelas->id}&tahun={$tahun->id}&siswa={$siswa->id}")
            ->assertOk()
            ->assertSee('Evaluasi dari URL bookmark.');
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
