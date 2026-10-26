<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_menampilkan_statistik_dari_database(): void
    {
        $user = User::factory()->create();
        Siswa::factory(3)->create();
        Guru::factory(2)->create();

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertViewHas('totalSiswa', 3)
            ->assertViewHas('totalGuru', 2);
    }
}
