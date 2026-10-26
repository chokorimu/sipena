<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelasiErdTest extends TestCase
{
    use RefreshDatabase;

    public function test_relasi_antar_entitas_berjalan(): void
    {
        $tahun = TahunAjaran::create(['tahun_ajaran' => '2024/2025', 'semester' => 'ganjil']);
        $guru = Guru::factory()->create();
        $kelas = Kelas::factory()->create(['wali_kelas_id' => $guru->id]);
        $siswa = Siswa::factory()->create();
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);
        $mapel = MataPelajaran::factory()->create();

        $this->assertTrue($siswa->kelas->contains($kelas));
        $this->assertTrue($kelas->siswa->contains($siswa));
        $this->assertTrue($kelas->waliKelas->is($guru));
        $this->assertTrue($guru->kelasWali->is($kelas));
        $this->assertEquals($tahun->id, $siswa->kelas->first()->pivot->tahun_id);
    }

    public function test_nilai_terhubung_ke_siswa_mapel_dan_tahun_ajaran(): void
    {
        $tahun = TahunAjaran::factory()->create();
        $siswa = Siswa::factory()->create();
        $mapel = MataPelajaran::factory()->create();

        $nilai = Nilai::create([
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'tahun_id' => $tahun->id,
            'formatif' => 80,
            'sumatif' => 90,
        ]);

        $this->assertTrue($nilai->siswa->is($siswa));
        $this->assertTrue($nilai->mataPelajaran->is($mapel));
        $this->assertTrue($nilai->tahunAjaran->is($tahun));
        $this->assertTrue($siswa->nilai->contains($nilai));
        $this->assertTrue($mapel->nilai->contains($nilai));
    }

    public function test_nilai_akhir_dihitung_otomatis_saat_menyimpan(): void
    {
        $nilai = Nilai::create([
            'siswa_id' => Siswa::factory()->create()->id,
            'mapel_id' => MataPelajaran::factory()->create()->id,
            'tahun_id' => TahunAjaran::factory()->create()->id,
            'formatif' => 80,
            'sumatif' => 90,
        ]);

        // 0,7 x 80 + 0,3 x 90 = 83
        $this->assertEquals(83.0, (float) $nilai->fresh()->nilai_akhir);
        $this->assertEquals('Lulus', $nilai->fresh()->status_kelulusan);
    }

    public function test_status_tidak_lulus_jika_di_bawah_kkm(): void
    {
        $nilai = Nilai::create([
            'siswa_id' => Siswa::factory()->create()->id,
            'mapel_id' => MataPelajaran::factory()->create()->id,
            'tahun_id' => TahunAjaran::factory()->create()->id,
            'formatif' => 60,
            'sumatif' => 65,
        ]);

        // 0,7 x 60 + 0,3 x 65 = 61,5 < KKM 75
        $this->assertEquals(61.5, (float) $nilai->fresh()->nilai_akhir);
        $this->assertEquals('Tidak Lulus', $nilai->fresh()->status_kelulusan);
    }

    public function test_jadwal_terhubung_ke_kelas_mapel_dan_guru(): void
    {
        $guru = Guru::factory()->create();
        $kelas = Kelas::factory()->create();
        $mapel = MataPelajaran::factory()->create();

        $jadwal = Jadwal::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guru->id,
            'hari' => 'senin',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '08:30:00',
        ]);

        $this->assertTrue($jadwal->kelas->is($kelas));
        $this->assertTrue($jadwal->mataPelajaran->is($mapel));
        $this->assertTrue($jadwal->guru->is($guru));
        $this->assertTrue($kelas->jadwal->contains($jadwal));
        $this->assertTrue($guru->jadwal->contains($jadwal));
    }
}
