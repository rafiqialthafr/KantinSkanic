<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_page_renders_without_main_navbar_and_footer(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke KantinSkanic');
        $response->assertDontSee('id="main-navbar"', false);
        $response->assertDontSee('Memangkas antrean siswa saat jam istirahat');
    }

    public function test_register_page_renders_without_main_navbar_and_footer(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Siswa');
        $response->assertDontSee('id="main-navbar"', false);
        $response->assertDontSee('Memangkas antrean siswa saat jam istirahat');
    }
}
