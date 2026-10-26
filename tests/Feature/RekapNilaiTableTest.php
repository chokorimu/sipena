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

class RekapNilaiTableTest extends TestCase
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

    public function test_menampilkan_rekap_nilai_siswa(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        Nilai::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 80,
            'sumatif' => 90,
        ]);

        Livewire::test('rekap-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->assertSee('Ahmad Fauzi')
            ->assertSee($siswa->nisn)
            // 0,7 x 80 + 0,3 x 90 = 83
            ->assertSee('83.0')
            ->assertSee('Lulus');
    }

    public function test_statistik_dihitung_dari_seluruh_siswa(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswaTinggi = Siswa::factory()->create(['nama' => 'Siti Nurhaliza']);
        $siswaRendah = Siswa::factory()->create(['nama' => 'Budi Santoso']);
        $siswaTinggi->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);
        $siswaRendah->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Nilai::create([
            'siswa_id' => $siswaTinggi->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 100,
            'sumatif' => 100,
        ]);

        Nilai::create([
            'siswa_id' => $siswaRendah->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 50,
            'sumatif' => 50,
        ]);

        Livewire::test('rekap-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            // rata-rata (100 + 50) / 2 = 75.0 — hanya muncul di stat card
            ->assertSee('75.0')
            ->assertSee('100.0')
            ->assertSee('50.0');
    }

    public function test_filter_mapel_hanya_menampilkan_nilai_mapel_terpilih(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        $mapelLain = MataPelajaran::factory()->create(['nama_mapel' => 'Bahasa Indonesia']);

        Nilai::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 80,
            'sumatif' => 90,
        ]);

        Livewire::test('rekap-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapelLain->id)
            ->assertSee('Ahmad Fauzi')
            // nilai 83.0 milik mapel Matematika tidak boleh tampil
            ->assertDontSee('83.0');
    }

    public function test_siswa_di_kelas_lain_tidak_tampil(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        $kelasLain = Kelas::factory()->create(['nama_kelas' => 'IX-B']);
        $siswaLain = Siswa::factory()->create(['nama' => 'Dewi Lestari']);
        $siswaLain->kelas()->attach($kelasLain->id, ['tahun_id' => $tahun->id]);

        Livewire::test('rekap-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->assertSee('Ahmad Fauzi')
            ->assertDontSee('Dewi Lestari');
    }

    public function test_siswa_tanpa_nilai_tampil_tanda_strip(): void
    {
        [$tahun, $kelas, $mapel, $siswa] = $this->siapkanData();

        Livewire::test('rekap-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->assertSee('Ahmad Fauzi')
            ->assertSee('-');
    }

    public function test_predikat_berdasarkan_nilai_akhir(): void
    {
        $kasus = [
            100.0 => 'A',
            95.0 => 'A',
            90.0 => 'A',
            89.9 => 'B',
            85.0 => 'B',
            80.0 => 'B',
            79.9 => 'C',
            75.0 => 'C',
            70.0 => 'C',
            69.9 => 'D',
            65.0 => 'D',
            60.0 => 'D',
            59.9 => 'E',
            50.0 => 'E',
        ];

        foreach ($kasus as $nilaiAkhir => $predikat) {
            $nilai = new Nilai;
            $nilai->nilai_akhir = $nilaiAkhir;

            $this->assertSame($predikat, $nilai->predikat(), "Nilai {$nilaiAkhir} seharusnya predikat {$predikat}");
        }
    }

    public function test_predikat_null_jika_nilai_akhir_kosong(): void
    {
        $nilai = new Nilai;

        $this->assertNull($nilai->predikat());
    }

    public function test_menampilkan_peringkat_siswa_berdasarkan_nilai_akhir(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $juara = Siswa::factory()->create(['nama' => 'Siti Nurhaliza']);
        $runnerUp = Siswa::factory()->create(['nama' => 'Budi Santoso']);
        $juara->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);
        $runnerUp->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Nilai::create([
            'siswa_id' => $juara->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 100,
            'sumatif' => 100,
        ]);

        Nilai::create([
            'siswa_id' => $runnerUp->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 50,
            'sumatif' => 50,
        ]);

        Livewire::test('rekap-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            // kolom No menampilkan peringkat; juara (nilai 100) tampil duluan
            ->assertSeeInOrder(['No', 'Nama Siswa'])
            ->assertSeeInOrder(['Siti Nurhaliza', 'Budi Santoso']);
    }

    public function test_default_sort_berdasarkan_peringkat(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $nilaiRendah = Siswa::factory()->create(['nama' => 'Ahmad Rendah']);
        $nilaiTinggi = Siswa::factory()->create(['nama' => 'Zainal Tinggi']);
        $tanpaNilai = Siswa::factory()->create(['nama' => 'Budi Kosong']);
        $nilaiRendah->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);
        $nilaiTinggi->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);
        $tanpaNilai->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Nilai::create([
            'siswa_id' => $nilaiRendah->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 50,
            'sumatif' => 50,
        ]);

        Nilai::create([
            'siswa_id' => $nilaiTinggi->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 100,
            'sumatif' => 100,
        ]);

        Livewire::test('rekap-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            // nilai tertinggi dulu, siswa tanpa nilai di bawah
            ->assertSeeInOrder([
                'Zainal Tinggi',
                'Ahmad Rendah',
                'Budi Kosong',
            ]);
    }

    public function test_sort_kolom_nama_mengubah_urutan(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswaZ = Siswa::factory()->create(['nama' => 'Zainal Tinggi']);
        $siswaA = Siswa::factory()->create(['nama' => 'Ahmad Rendah']);
        $siswaZ->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);
        $siswaA->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Nilai::create([
            'siswa_id' => $siswaZ->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 100,
            'sumatif' => 100,
        ]);

        Nilai::create([
            'siswa_id' => $siswaA->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 50,
            'sumatif' => 50,
        ]);

        Livewire::test('rekap-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            // default: Zainal (nilai 100) dulu
            ->assertSeeInOrder(['Zainal Tinggi', 'Ahmad Rendah'])
            ->call('sort', 'nama')
            ->assertSet('sortBy', 'nama')
            ->assertSet('sortDirection', 'asc')
            // sort nama asc: Ahmad dulu
            ->assertSeeInOrder(['Ahmad Rendah', 'Zainal Tinggi'])
            // klik kolom sama lagi → balik arah
            ->call('sort', 'nama')
            ->assertSet('sortDirection', 'desc')
            ->assertSeeInOrder(['Zainal Tinggi', 'Ahmad Rendah']);
    }

    public function test_sort_nilai_akhir_asc_menaruh_siswa_tanpa_nilai_di_bawah(): void
    {
        [$tahun, $kelas, $mapel] = $this->siapkanData();

        $siswaTinggi = Siswa::factory()->create(['nama' => 'Ahmad Tinggi']);
        $siswaRendah = Siswa::factory()->create(['nama' => 'Zainal Rendah']);
        $siswaKosong = Siswa::factory()->create(['nama' => 'Budi Kosong']);
        $siswaTinggi->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);
        $siswaRendah->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);
        $siswaKosong->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Nilai::create([
            'siswa_id' => $siswaTinggi->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 80,
            'sumatif' => 80,
        ]);

        Nilai::create([
            'siswa_id' => $siswaRendah->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 60,
            'sumatif' => 60,
        ]);

        Livewire::test('rekap-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            // klik pertama kolom numerik: desc
            ->call('sort', 'nilai_akhir')
            ->assertSet('sortDirection', 'desc')
            ->assertSeeInOrder(['Ahmad Tinggi', 'Zainal Rendah', 'Budi Kosong'])
            // klik lagi: asc, siswa tanpa nilai tetap di bawah
            ->call('sort', 'nilai_akhir')
            ->assertSet('sortDirection', 'asc')
            ->assertSeeInOrder(['Zainal Rendah', 'Ahmad Tinggi', 'Budi Kosong']);
    }

    public function test_hitung_ranking_dengan_nilai_seri(): void
    {
        $nilai = collect([90.0, 80.0, 80.0, 70.0])->mapWithKeys(fn (float $nilaiAkhir, int $index): array => [
            $index + 1 => tap(new Nilai, function (Nilai $item) use ($nilaiAkhir): void {
                $item->nilai_akhir = $nilaiAkhir;
            }),
        ]);

        // standard competition: 1, 2, 2, 4
        $this->assertSame([1 => 1, 2 => 2, 3 => 2, 4 => 4], Nilai::hitungRanking($nilai));
    }

    public function test_hitung_ranking_mengabaikan_siswa_tanpa_nilai(): void
    {
        $nilai = collect([
            1 => tap(new Nilai, function (Nilai $item): void {
                $item->nilai_akhir = 90.0;
            }),
            2 => new Nilai,
        ]);

        $this->assertSame([1 => 1], Nilai::hitungRanking($nilai));
    }
}
