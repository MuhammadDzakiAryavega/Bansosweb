<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kewenangan admin & seksi dipisah tegas: admin memegang konten portal dan
 * akun pengguna, seksi memegang PKH, pengaduan, dan arsip. Tes ini menjaga
 * agar pemisahan tetap ditegakkan di tingkat rute, bukan sekadar disembunyikan
 * dari menu sidebar.
 */
class SmokePemisahanPeranTest extends TestCase
{
    use RefreshDatabase;

    /** Rute yang hanya boleh dibuka administrator. */
    private const MILIK_ADMIN = [
        '/admin/pkh/kriteria',
        '/admin/berita',
        '/admin/berita/tambah',
        '/admin/galeri',
        '/admin/pengguna',
        '/admin/pengguna/tambah',
    ];

    /** Rute yang hanya boleh dibuka seksi. */
    private const MILIK_SEKSI = [
        '/admin/pkh/pendaftaran',
        '/admin/pkh/penilaian',
        '/admin/pkh/hasil-akhir',
        '/admin/pengaduan',
        '/admin/arsip',
    ];

    private function petugas(string $role): User
    {
        return User::create([
            'name'     => 'Petugas ' . $role,
            'nik'      => $role === 'admin' ? '1212121212121212' : '3434343434343434',
            'email'    => $role . '.peran@example.com',
            'password' => 'rahasia123',
            'role'     => $role,
        ]);
    }

    public function test_seksi_tidak_dapat_mengelola_pengguna_berita_dan_galeri(): void
    {
        $seksi = $this->petugas('seksi');

        foreach (self::MILIK_ADMIN as $url) {
            $this->actingAs($seksi)->get($url)->assertForbidden();
        }
    }

    public function test_admin_tidak_dapat_membuka_modul_milik_seksi(): void
    {
        $admin = $this->petugas('admin');

        foreach (self::MILIK_SEKSI as $url) {
            $this->actingAs($admin)->get($url)->assertForbidden();
        }
    }

    public function test_masing_masing_peran_dapat_membuka_modulnya_sendiri(): void
    {
        $admin = $this->petugas('admin');
        $seksi = $this->petugas('seksi');

        foreach (self::MILIK_ADMIN as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        foreach (self::MILIK_SEKSI as $url) {
            $this->actingAs($seksi)->get($url)->assertOk();
        }
    }

    public function test_seksi_dapat_masuk_dashboard_dan_melihat_menunya_saja(): void
    {
        $seksi = $this->petugas('seksi');

        $this->actingAs($seksi)->get('/dashboard')->assertOk()
            ->assertSee('PANEL SEKSI')
            ->assertSee('Pendaftaran Masuk')
            ->assertSee('Penilaian Calon')
            ->assertSee('Hasil Akhir')
            ->assertSee('Kelola Arsip')
            ->assertDontSee('Kelola Kriteria')
            ->assertDontSee('Kelola Pengguna')
            ->assertDontSee('Kelola Berita')
            ->assertDontSee('Kelola Galeri');
    }

    public function test_dashboard_admin_tidak_menawarkan_pintasan_milik_seksi(): void
    {
        $admin = $this->petugas('admin');

        $this->actingAs($admin)->get('/dashboard')->assertOk()
            ->assertSee('PANEL ADMIN')
            ->assertSee('Kelola Pengguna')
            ->assertSee('Kelola Kriteria')
            ->assertDontSee('Pendaftaran Masuk')
            ->assertDontSee('Penilaian Calon')
            ->assertDontSee('Hasil Akhir')
            ->assertDontSee('Kelola Arsip')
            ->assertDontSee('Kelola Pengaduan');
    }

    public function test_seksi_masuk_langsung_ke_dashboard_setelah_login(): void
    {
        $this->petugas('seksi');

        $this->post('/login', [
            'email'    => 'seksi.peran@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_akun_seksi_tetap_dapat_dikelola_administrator(): void
    {
        $admin = $this->petugas('admin');

        $this->actingAs($admin)->post('/admin/pengguna', [
            'name'                  => 'Petugas Seksi Baru',
            'nik'                   => '5656565656565656',
            'email'                 => 'seksi.baru@example.com',
            'role'                  => 'seksi',
            'password'              => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('admin.user.index'));

        $baru = User::where('email', 'seksi.baru@example.com')->firstOrFail();
        $this->assertSame('seksi', $baru->role);
        $this->assertTrue($baru->isSeksi());
        $this->assertTrue($baru->isPetugas());
        $this->assertFalse($baru->isAdmin());
    }
}
