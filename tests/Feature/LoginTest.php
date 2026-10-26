<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_diakses_tamu(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_pengguna_dapat_login_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create([
            'username' => 'guru.test',
            'password' => Hash::make('rahasia123'),
        ]);

        Livewire::test('pages::auth.login')
            ->set('username', 'guru.test')
            ->set('password', 'rahasia123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        User::factory()->create([
            'username' => 'guru.test',
            'password' => Hash::make('rahasia123'),
        ]);

        Livewire::test('pages::auth.login')
            ->set('username', 'guru.test')
            ->set('password', 'password-salah')
            ->call('login')
            ->assertHasErrors('username');

        $this->assertGuest();
    }

    public function test_login_gagal_jika_kolom_kosong(): void
    {
        Livewire::test('pages::auth.login')
            ->set('username', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['username', 'password']);

        $this->assertGuest();
    }

    public function test_pengguna_terautentikasi_dapat_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_pengguna_terautentikasi_dapat_mengakses_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertViewHas('totalSiswa');
    }
}
