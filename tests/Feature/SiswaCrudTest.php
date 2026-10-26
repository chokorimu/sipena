<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiswaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_menambah_siswa_baru(): void
    {
        $kelas = Kelas::factory()->create(['nama_kelas' => 'VII-A']);
        $tahun = TahunAjaran::factory()->create();

        Livewire::test('siswa-table')
            ->call('tambah')
            ->assertDispatched('open-modal')
            ->set('nisn', '1234567890')
            ->set('nama', 'Ahmad Fauzi')
            ->set('jenis_kelamin', 'L')
            ->set('tanggal_lahir', '2010-05-15')
            ->set('alamat', 'Jl. Pattimura No. 1')
            ->set('formKelasId', (string) $kelas->id)
            ->set('formTahunId', (string) $tahun->id)
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal');

        $this->assertDatabaseCount('siswa', 1);
        $this->assertDatabaseHas('siswa', [
            'nisn' => '1234567890',
            'nama' => 'Ahmad Fauzi',
            'jenis_kelamin' => 'L',
        ]);

        $siswa = Siswa::first();
        $this->assertTrue($siswa->kelas->contains($kelas->id));
    }

    public function test_mengedit_siswa(): void
    {
        $kelas = Kelas::factory()->create();
        $tahun = TahunAjaran::factory()->create();
        $siswa = Siswa::factory()->create(['nama' => 'Ahmad Fauzi', 'nisn' => '1234567890']);
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Livewire::test('siswa-table')
            ->call('edit', $siswa->id)
            ->assertDispatched('open-modal')
            ->assertSet('nisn', '1234567890')
            ->assertSet('nama', 'Ahmad Fauzi')
            ->set('nama', 'Ahmad Fauzi Rahman')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('siswa', [
            'id' => $siswa->id,
            'nama' => 'Ahmad Fauzi Rahman',
        ]);
        $this->assertDatabaseCount('siswa', 1);
    }

    public function test_menghapus_siswa(): void
    {
        $siswa = Siswa::factory()->create(['nama' => 'Ahmad Fauzi']);

        Livewire::test('siswa-table')
            ->call('konfirmasiHapus', $siswa->id)
            ->assertDispatched('open-confirm')
            ->assertSet('siswaHapusNama', 'Ahmad Fauzi')
            ->call('hapusSekarang')
            ->assertDispatched('close-confirm');

        $this->assertDatabaseCount('siswa', 0);
    }

    public function test_nisn_harus_10_digit(): void
    {
        $kelas = Kelas::factory()->create();
        $tahun = TahunAjaran::factory()->create();

        Livewire::test('siswa-table')
            ->call('tambah')
            ->set('nisn', '123')
            ->set('nama', 'Ahmad Fauzi')
            ->set('tanggal_lahir', '2010-05-15')
            ->set('formKelasId', (string) $kelas->id)
            ->set('formTahunId', (string) $tahun->id)
            ->call('simpan')
            ->assertHasErrors('nisn');

        $this->assertDatabaseCount('siswa', 0);
    }

    public function test_nisn_tidak_boleh_duplikat(): void
    {
        Siswa::factory()->create(['nisn' => '1234567890']);

        $kelas = Kelas::factory()->create();
        $tahun = TahunAjaran::factory()->create();

        Livewire::test('siswa-table')
            ->call('tambah')
            ->set('nisn', '1234567890')
            ->set('nama', 'Ahmad Fauzi')
            ->set('tanggal_lahir', '2010-05-15')
            ->set('formKelasId', (string) $kelas->id)
            ->set('formTahunId', (string) $tahun->id)
            ->call('simpan')
            ->assertHasErrors('nisn');

        $this->assertDatabaseCount('siswa', 1);
    }

    public function test_nisn_yang_sama_diizinkan_saat_edit_sendiri(): void
    {
        $kelas = Kelas::factory()->create();
        $tahun = TahunAjaran::factory()->create();
        $siswa = Siswa::factory()->create(['nisn' => '1234567890']);
        $siswa->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Livewire::test('siswa-table')
            ->call('edit', $siswa->id)
            ->set('nisn', '1234567890')
            ->call('simpan')
            ->assertHasNoErrors();
    }

    public function test_validasi_kolom_wajib(): void
    {
        Livewire::test('siswa-table')
            ->call('tambah')
            ->set('nisn', '')
            ->set('nama', '')
            ->set('tanggal_lahir', '')
            ->call('simpan')
            ->assertHasErrors(['nisn', 'nama', 'tanggal_lahir', 'formKelasId', 'formTahunId']);
    }

    public function test_menambah_siswa_menetapkan_kelas_untuk_tahun_terpilih(): void
    {
        $kelas = Kelas::factory()->create();
        $tahun = TahunAjaran::factory()->create();

        Livewire::test('siswa-table')
            ->call('tambah')
            ->set('nisn', '9999999999')
            ->set('nama', 'Siti Aminah')
            ->set('tanggal_lahir', '2011-02-20')
            ->set('formKelasId', (string) $kelas->id)
            ->set('formTahunId', (string) $tahun->id)
            ->call('simpan')
            ->assertHasNoErrors();

        $siswa = Siswa::first();

        $this->assertDatabaseHas('kelas_siswa', [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_id' => $tahun->id,
        ]);
    }
}
