<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['sipena.registrasi_dibuka' => true]);
    }

    public function test_halaman_register_dapat_diakses_tamu(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Buat Akun SiPena');
    }

    public function test_tamu_dapat_mendaftar_dan_langsung_masuk(): void
    {
        Livewire::test('pages::auth.register')
            ->set('name', 'Siti Nurhaliza')
            ->set('username', 'siti.nurhaliza')
            ->set('email', 'siti@mtsnambon.sch.id')
            ->set('password', 'rahasia123')
            ->set('passwordConfirmation', 'rahasia123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $user = User::query()->where('username', 'siti.nurhaliza')->first();

        $this->assertNotNull($user);
        $this->assertSame('Siti Nurhaliza', $user->name);
        $this->assertSame('siti@mtsnambon.sch.id', $user->email);
        $this->assertAuthenticatedAs($user);
    }

    public function test_pendaftaran_mandiri_selalu_berperan_guru_bukan_admin(): void
    {
        Livewire::test('pages::auth.register')
            ->set('name', 'Budi Santoso')
            ->set('username', 'budi.santoso')
            ->set('password', 'rahasia123')
            ->set('passwordConfirmation', 'rahasia123')
            ->call('register')
            ->assertHasNoErrors();

        $user = User::query()->where('username', 'budi.santoso')->first();

        $this->assertNotNull($user);
        $this->assertSame('guru', $user->role);
        $this->assertFalse($user->isAdmin());
    }

    public function test_email_opsional_boleh_dikosongkan(): void
    {
        Livewire::test('pages::auth.register')
            ->set('name', 'Tanpa Email')
            ->set('username', 'tanpa.email')
            ->set('email', '')
            ->set('password', 'rahasia123')
            ->set('passwordConfirmation', 'rahasia123')
            ->call('register')
            ->assertHasNoErrors();

        $user = User::query()->where('username', 'tanpa.email')->first();

        $this->assertNotNull($user);
        $this->assertNull($user->email);
    }

    public function test_registrasi_gagal_jika_username_sudah_dipakai(): void
    {
        User::factory()->create(['username' => 'sudah.ada']);

        Livewire::test('pages::auth.register')
            ->set('name', 'Pendaftar Baru')
            ->set('username', 'sudah.ada')
            ->set('password', 'rahasia123')
            ->set('passwordConfirmation', 'rahasia123')
            ->call('register')
            ->assertHasErrors(['username' => 'unique']);

        $this->assertGuest();
    }

    public function test_registrasi_gagal_jika_konfirmasi_sandi_tidak_cocok(): void
    {
        Livewire::test('pages::auth.register')
            ->set('name', 'Pendaftar Baru')
            ->set('username', 'pendaftar.baru')
            ->set('password', 'rahasia123')
            ->set('passwordConfirmation', 'beda12345')
            ->call('register')
            ->assertHasErrors('password');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['username' => 'pendaftar.baru']);
    }

    public function test_registrasi_gagal_jika_kata_sandi_kurang_dari_8_karakter(): void
    {
        Livewire::test('pages::auth.register')
            ->set('name', 'Pendaftar Baru')
            ->set('username', 'pendaftar.baru')
            ->set('password', 'pendek')
            ->set('passwordConfirmation', 'pendek')
            ->call('register')
            ->assertHasErrors(['password' => 'min']);

        $this->assertGuest();
    }

    public function test_registrasi_gagal_jika_kolom_wajib_kosong(): void
    {
        Livewire::test('pages::auth.register')
            ->set('name', '')
            ->set('username', '')
            ->set('password', '')
            ->set('passwordConfirmation', '')
            ->call('register')
            ->assertHasErrors(['name', 'username', 'password']);

        $this->assertGuest();
    }

    public function test_halaman_register_tertutup_saat_config_dinonaktifkan(): void
    {
        config(['sipena.registrasi_dibuka' => false]);

        $this->get('/register')->assertForbidden();
    }

    public function test_tautan_daftar_hilang_dari_login_saat_registrasi_ditutup(): void
    {
        config(['sipena.registrasi_dibuka' => false]);

        $this->get('/login')
            ->assertOk()
            ->assertDontSee('Daftar di sini');
    }

    public function test_tautan_daftar_tampil_di_login_saat_registrasi_dibuka(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Daftar di sini');
    }
}
