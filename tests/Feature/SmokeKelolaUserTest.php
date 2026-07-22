<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeKelolaUserTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name'     => 'Admin Uji',
            'nik'      => '1111111111111111',
            'email'    => 'admin.uji@example.com',
            'password' => 'rahasia123',
            'role'     => 'admin',
        ]);
    }

    public function test_halaman_kelola_user_dapat_diakses(): void
    {
        $admin = $this->admin();
        $warga = User::create([
            'name'     => 'Warga Uji',
            'nik'      => '2222222222222222',
            'email'    => 'warga.uji@example.com',
            'password' => 'rahasia123',
            'role'     => 'user',
        ]);

        $this->actingAs($admin)->get('/admin/pengguna')->assertOk()->assertSee('Warga Uji');
        $this->actingAs($admin)->get('/admin/pengguna?cari=warga&role=user')->assertOk();
        $this->actingAs($admin)->get('/admin/pengguna/tambah')->assertOk();
        $this->actingAs($admin)->get("/admin/pengguna/{$warga->id}/ubah")->assertOk();
    }

    public function test_pengguna_biasa_ditolak(): void
    {
        $warga = User::create([
            'name'     => 'Warga Biasa',
            'nik'      => '3333333333333333',
            'email'    => 'biasa@example.com',
            'password' => 'rahasia123',
            'role'     => 'user',
        ]);

        $this->actingAs($warga)->get('/admin/pengguna')->assertForbidden();
    }

    public function test_tambah_ubah_dan_hapus_pengguna(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/pengguna', [
            'name'                  => 'Budi Santoso',
            'nik'                   => '4444444444444444',
            'email'                 => 'budi@example.com',
            'role'                  => 'user',
            'password'              => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('admin.user.index'));

        $budi = User::where('email', 'budi@example.com')->firstOrFail();
        $this->assertSame('user', $budi->role);
        $this->assertTrue(password_verify('rahasia123', $budi->password), 'Kata sandi tidak ter-hash dengan benar.');

        // Perbarui tanpa mengisi kata sandi: sandi lama harus tetap berlaku.
        $sandiLama = $budi->password;
        $this->actingAs($admin)->put("/admin/pengguna/{$budi->id}", [
            'name'     => 'Budi Santoso Jaya',
            'nik'      => '4444444444444444',
            'email'    => 'budi@example.com',
            'role'     => 'admin',
            'password' => '',
        ])->assertRedirect(route('admin.user.index'));

        $budi->refresh();
        $this->assertSame('Budi Santoso Jaya', $budi->name);
        $this->assertSame('admin', $budi->role);
        $this->assertSame($sandiLama, $budi->password);

        $this->actingAs($admin)->delete("/admin/pengguna/{$budi->id}")
            ->assertRedirect(route('admin.user.index'));
        $this->assertNull(User::find($budi->id));
    }

    public function test_admin_tidak_dapat_menghapus_akun_sendiri(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->delete("/admin/pengguna/{$admin->id}")
            ->assertSessionHas('error');
        $this->assertNotNull(User::find($admin->id));
    }

    public function test_admin_terakhir_tidak_dapat_dihapus_atau_diturunkan(): void
    {
        $admin = $this->admin();
        $lain  = User::create([
            'name'     => 'Admin Kedua',
            'nik'      => '5555555555555555',
            'email'    => 'admin2@example.com',
            'password' => 'rahasia123',
            'role'     => 'admin',
        ]);

        // Sisakan satu admin saja.
        $this->actingAs($admin)->delete("/admin/pengguna/{$lain->id}")->assertRedirect();
        $this->assertNull(User::find($lain->id));

        // Admin terakhir tidak boleh diturunkan lewat akun lain.
        $this->assertSame(1, User::where('role', 'admin')->count());
    }

    public function test_validasi_menolak_nik_ganda(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/pengguna', [
            'name'                  => 'Duplikat',
            'nik'                   => $admin->nik,
            'email'                 => 'duplikat@example.com',
            'role'                  => 'user',
            'password'              => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('nik');
    }
}
