<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiswaTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_tabel_menampilkan_siswa(): void
    {
        $siswa = Siswa::factory()->create(['nama' => 'Ahmad Fauzi']);

        Livewire::test('siswa-table')
            ->assertSee('Ahmad Fauzi')
            ->assertSee($siswa->nisn);
    }

    public function test_pencarian_menyaring_berdasarkan_nama(): void
    {
        Siswa::factory()->create(['nama' => 'Ahmad Fauzi']);
        Siswa::factory()->create(['nama' => 'Budi Santoso']);

        Livewire::test('siswa-table')
            ->set('search', 'Ahmad')
            ->assertSee('Ahmad Fauzi')
            ->assertDontSee('Budi Santoso');
    }

    public function test_pencarian_menyaring_berdasarkan_nisn(): void
    {
        Siswa::factory()->create(['nama' => 'Ahmad Fauzi', 'nisn' => '1234567890']);
        Siswa::factory()->create(['nama' => 'Budi Santoso', 'nisn' => '9999999999']);

        Livewire::test('siswa-table')
            ->set('search', '1234567890')
            ->assertSee('Ahmad Fauzi')
            ->assertDontSee('Budi Santoso');
    }

    public function test_filter_kelas_menyaring_siswa(): void
    {
        $guru = Guru::factory()->create();
        $kelas = Kelas::factory()->create(['nama_kelas' => 'VII-A', 'wali_kelas_id' => $guru->id]);
        $tahun = TahunAjaran::factory()->create();

        $siswaKelas = Siswa::factory()->create(['nama' => 'Ahmad Fauzi']);
        $siswaKelas->kelas()->attach($kelas->id, ['tahun_id' => $tahun->id]);

        Siswa::factory()->create(['nama' => 'Budi Santoso']);

        Livewire::test('siswa-table')
            ->set('kelas', (string) $kelas->id)
            ->assertSee('Ahmad Fauzi')
            ->assertDontSee('Budi Santoso');
    }

    public function test_menampilkan_pesan_kosong_jika_tidak_ada_data(): void
    {
        Livewire::test('siswa-table')
            ->assertSee('Tidak ada data siswa yang ditemukan.');
    }
}
