<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeLoginTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(string $role, string $email): User
    {
        return User::create([
            'name'     => $role === 'admin' ? 'Admin Portal' : 'Warga Portal',
            'nik'      => $role === 'admin' ? '1111111111111111' : '2222222222222222',
            'email'    => $email,
            'password' => 'rahasia123',
            'role'     => $role,
        ]);
    }

    private function masuk(string $email): \Illuminate\Testing\TestResponse
    {
        return $this->post('/login', [
            'email'    => $email,
            'password' => 'rahasia123',
        ]);
    }

    public function test_admin_masuk_langsung_ke_dashboard(): void
    {
        $this->buatUser('admin', 'admin.login@example.com');

        $this->masuk('admin.login@example.com')->assertRedirect(route('dashboard'));
    }

    public function test_admin_tidak_terlempar_ke_halaman_pengguna_yang_sempat_dibuka_saat_tamu(): void
    {
        $this->buatUser('admin', 'admin.login@example.com');

        // Tamu menekan menu "Pengaduan" di navbar, lalu diminta login.
        $this->get('/pengaduan')->assertRedirect(route('login'));

        $this->masuk('admin.login@example.com')->assertRedirect(route('dashboard'));
    }

    public function test_admin_tetap_diantar_ke_halaman_admin_yang_dituju(): void
    {
        $this->buatUser('admin', 'admin.login@example.com');

        $this->get('/admin/berita')->assertRedirect(route('login'));

        $this->masuk('admin.login@example.com')->assertRedirect(url('/admin/berita'));
    }

    public function test_pengguna_diantar_ke_halaman_yang_tadinya_dituju(): void
    {
        $this->buatUser('user', 'warga.login@example.com');

        $this->get('/pengaduan')->assertRedirect(route('login'));

        $this->masuk('warga.login@example.com')->assertRedirect(url('/pengaduan'));
    }

    public function test_pengguna_biasa_tidak_diarahkan_ke_area_admin(): void
    {
        $this->buatUser('user', 'warga.login@example.com');

        $this->get('/admin/berita')->assertRedirect(route('login'));

        $this->masuk('warga.login@example.com')->assertRedirect(route('home'));
    }
}
