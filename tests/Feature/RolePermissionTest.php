<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Bukti bahwa role belum membatasi apa pun di repo ini: user "guru" bisa
 * melakukan semua yang bisa dilakukan admin lewat rute dan komponen yang sama.
 *
 * Test ini adalah dokumentasi perilaku saat ini. Kalau nanti role mulai
 * membatasi akses, test ini harus diperbarui.
 */
class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    private function userGuru(): User
    {
        return User::factory()->create(['role' => 'guru']);
    }

    public function test_user_guru_bisa_akses_semua_halaman_fitur(): void
    {
        $guru = $this->userGuru();

        foreach (['/', '/data-siswa', '/data-guru', '/input-nilai', '/rekap-nilai', '/laporan'] as $url) {
            $this->actingAs($guru)->get($url)->assertOk();
        }
    }

    public function test_user_guru_bisa_tambah_dan_hapus_siswa(): void
    {
        $guru = $this->userGuru();

        $tahun = TahunAjaran::factory()->create(['semester' => 'ganjil']);
        $kelas = Kelas::factory()->create();

        Livewire::actingAs($guru)
            ->test('siswa-table')
            ->set('nama', 'Siswa Uji')
            ->set('nisn', '1234567890')
            ->set('tanggal_lahir', '2010-05-17')
            ->set('formKelasId', (string) $kelas->id)
            ->set('formTahunId', (string) $tahun->id)
            ->call('simpan')
            ->assertHasNoErrors();

        $siswa = Siswa::query()->where('nama', 'Siswa Uji')->first();
        $this->assertNotNull($siswa);

        Livewire::actingAs($guru)
            ->test('siswa-table')
            ->set('siswaHapusId', $siswa->id)
            ->call('hapusSekarang');

        $this->assertDatabaseMissing('siswa', ['id' => $siswa->id]);
    }

    public function test_user_guru_bisa_tambah_dan_hapus_guru(): void
    {
        $guru = $this->userGuru();

        Livewire::actingAs($guru)
            ->test('guru-table')
            ->set('nama_guru', 'Guru Uji')
            ->set('nip', '198765432109876543')
            ->call('simpan')
            ->assertHasNoErrors();

        $record = Guru::query()->where('nama_guru', 'Guru Uji')->first();
        $this->assertNotNull($record);

        Livewire::actingAs($guru)
            ->test('guru-table')
            ->set('guruHapusId', $record->id)
            ->call('hapusSekarang');

        $this->assertDatabaseMissing('guru', ['id' => $record->id]);
    }

    public function test_user_guru_bisa_menyimpan_nilai_siswa(): void
    {
        $guru = $this->userGuru();

        $tahun = TahunAjaran::factory()->create(['semester' => 'ganjil']);
        $kelas = Kelas::factory()->create();
        $mapel = MataPelajaran::factory()->create();
        $siswa = Siswa::factory()->create();
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Livewire::actingAs($guru)
            ->test('input-nilai-table')
            ->set('tahunId', (string) $tahun->id)
            ->set('kelasId', (string) $kelas->id)
            ->set('mapelId', (string) $mapel->id)
            ->set("nilai.{$siswa->id}.formatif", '80')
            ->set("nilai.{$siswa->id}.sumatif", '90')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('nilai', [
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
        ]);

        $nilai = Nilai::query()
            ->where('siswa_id', $siswa->id)
            ->where('mapel_id', $mapel->id)
            ->first();

        $this->assertSame(83.0, (float) $nilai->nilai_akhir);
    }

    public function test_user_guru_bisa_export_csv_rekap_nilai(): void
    {
        $guru = $this->userGuru();

        $kelas = Kelas::factory()->create();
        $tahun = TahunAjaran::factory()->create(['semester' => 'ganjil']);
        $mapel = MataPelajaran::factory()->create();
        $siswa = Siswa::factory()->create();
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        $this->actingAs($guru)
            ->get("/rekap-nilai/export?kelas={$kelas->id}&tahun={$tahun->id}&mapel={$mapel->id}")
            ->assertOk();
    }

    public function test_user_guru_bisa_export_csv_laporan(): void
    {
        $guru = $this->userGuru();

        $kelas = Kelas::factory()->create();
        $tahun = TahunAjaran::factory()->create(['semester' => 'ganjil']);
        $siswa = Siswa::factory()->create();
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        $this->actingAs($guru)
            ->get("/laporan/export?kelas={$kelas->id}&tahun={$tahun->id}&siswa={$siswa->id}")
            ->assertOk();
    }

    public function test_user_guru_bisa_unduh_pdf_laporan(): void
    {
        $guru = $this->userGuru();

        $kelas = Kelas::factory()->create();
        $tahun = TahunAjaran::factory()->create(['semester' => 'ganjil']);
        $siswa = Siswa::factory()->create();
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        $kueri = "kelas={$kelas->id}&tahun={$tahun->id}&siswa={$siswa->id}";

        $this->actingAs($guru)->get("/laporan/pdf?{$kueri}")->assertOk();
    }

    public function test_user_guru_bisa_lihat_semua_jenis_laporan(): void
    {
        $guru = $this->userGuru();

        $kelas = Kelas::factory()->create();
        $tahun = TahunAjaran::factory()->create(['semester' => 'ganjil']);

        foreach (['rekap-kelas', 'rapor-siswa', 'analisis-nilai'] as $jenis) {
            Livewire::actingAs($guru)
                ->test('laporan-table')
                ->set('kelasId', (string) $kelas->id)
                ->set('tahunId', (string) $tahun->id)
                ->set('jenis', $jenis)
                ->assertOk();
        }
    }

    public function test_role_hanya_membedakan_label_bukan_akses(): void
    {
        $guru = $this->userGuru();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($guru->isAdmin());
        $this->assertTrue($admin->isAdmin());

        // Halaman, aksi, dan data yang bisa diakses identik.
        $this->actingAs($guru)->get('/')->assertOk();
        $this->actingAs($admin)->get('/')->assertOk();
    }
}
