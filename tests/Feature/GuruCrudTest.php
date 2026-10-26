<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuruCrudTest extends TestCase
{
    use RefreshDatabase;

    private const NIP_VALID = '123456789012345678';

    public function test_menambah_guru_baru(): void
    {
        Livewire::test('guru-table')
            ->call('tambah')
            ->assertDispatched('open-modal')
            ->set('nip', self::NIP_VALID)
            ->set('nama_guru', 'Budi Santoso')
            ->set('jenis_kelamin', 'L')
            ->set('no_hp', '081234567890')
            ->set('alamat', 'Jl. Pattimura No. 1')
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal');

        $this->assertDatabaseCount('guru', 1);
        $this->assertDatabaseHas('guru', [
            'nip' => self::NIP_VALID,
            'nama_guru' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567890',
        ]);
    }

    public function test_mengedit_guru(): void
    {
        $guru = Guru::factory()->create(['nama_guru' => 'Budi Santoso']);

        Livewire::test('guru-table')
            ->call('edit', $guru->id)
            ->assertDispatched('open-modal')
            ->assertSet('nip', $guru->nip)
            ->assertSet('nama_guru', 'Budi Santoso')
            ->set('nama_guru', 'Budi Santoso S.Pd')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('guru', [
            'id' => $guru->id,
            'nama_guru' => 'Budi Santoso S.Pd',
        ]);
        $this->assertDatabaseCount('guru', 1);
    }

    public function test_menghapus_guru(): void
    {
        $guru = Guru::factory()->create(['nama_guru' => 'Budi Santoso']);

        Livewire::test('guru-table')
            ->call('konfirmasiHapus', $guru->id)
            ->assertDispatched('open-confirm')
            ->assertSet('guruHapusNama', 'Budi Santoso')
            ->call('hapusSekarang')
            ->assertDispatched('close-confirm');

        $this->assertDatabaseCount('guru', 0);
    }

    public function test_nip_harus_18_digit(): void
    {
        Livewire::test('guru-table')
            ->call('tambah')
            ->set('nip', '123')
            ->set('nama_guru', 'Budi Santoso')
            ->call('simpan')
            ->assertHasErrors('nip');

        $this->assertDatabaseCount('guru', 0);
    }

    public function test_nip_tidak_boleh_duplikat(): void
    {
        Guru::factory()->create(['nip' => self::NIP_VALID]);

        Livewire::test('guru-table')
            ->call('tambah')
            ->set('nip', self::NIP_VALID)
            ->set('nama_guru', 'Budi Santoso')
            ->call('simpan')
            ->assertHasErrors('nip');

        $this->assertDatabaseCount('guru', 1);
    }

    public function test_nip_yang_sama_diizinkan_saat_edit_sendiri(): void
    {
        $guru = Guru::factory()->create(['nip' => self::NIP_VALID]);

        Livewire::test('guru-table')
            ->call('edit', $guru->id)
            ->set('nip', self::NIP_VALID)
            ->call('simpan')
            ->assertHasNoErrors();
    }

    public function test_validasi_kolom_wajib(): void
    {
        Livewire::test('guru-table')
            ->call('tambah')
            ->set('nip', '')
            ->set('nama_guru', '')
            ->call('simpan')
            ->assertHasErrors(['nip', 'nama_guru']);
    }

    public function test_mencari_guru_berdasarkan_nama_atau_nip(): void
    {
        $guru = Guru::factory()->create(['nama_guru' => 'Budi Santoso', 'nip' => self::NIP_VALID]);
        Guru::factory()->create(['nama_guru' => 'Siti Aminah']);

        Livewire::test('guru-table')
            ->set('search', 'Budi')
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Aminah')
            ->set('search', self::NIP_VALID)
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Aminah');

        $this->assertSame($guru->id, $guru->id);
    }

    public function test_menghapus_guru_menghapus_jadwal_dan_mengosongkan_wali_kelas(): void
    {
        $guru = Guru::factory()->create();
        $kelas = Kelas::factory()->create(['wali_kelas_id' => $guru->id]);
        Jadwal::factory()->create(['guru_id' => $guru->id]);

        Livewire::test('guru-table')
            ->call('konfirmasiHapus', $guru->id)
            ->call('hapusSekarang');

        $this->assertDatabaseCount('guru', 0);
        // FK jadwal.guru_id cascadeOnDelete
        $this->assertDatabaseCount('jadwal', 0);
        // FK kelas.wali_kelas_id nullOnDelete
        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'wali_kelas_id' => null,
        ]);
    }
}
