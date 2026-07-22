<?php

namespace Tests\Feature;

use App\Models\Galeri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SmokeKelolaGaleriTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name'     => 'Admin Galeri',
            'nik'      => '6666666666666666',
            'email'    => 'admin.galeri@example.com',
            'password' => 'rahasia123',
            'role'     => 'admin',
        ]);
    }

    private function galeri(string $judul = 'Penyaluran Bantuan Tahap I'): Galeri
    {
        return Galeri::create([
            'judul_kegiatan'    => $judul,
            'slug'              => Galeri::buatSlug($judul),
            'tgl_pelaksanaan'   => '2026-05-10',
            'deskripsi_singkat' => 'Kegiatan penyaluran bantuan di Kecamatan Teramang Jaya.',
        ]);
    }

    public function test_halaman_kelola_galeri_dapat_diakses(): void
    {
        $admin  = $this->admin();
        $galeri = $this->galeri();
        $galeri->fotos()->create(['path' => '/storage/galeri/contoh.jpg', 'urutan' => 1]);

        $this->actingAs($admin)->get('/admin/galeri')->assertOk()
            ->assertSee('Penyaluran Bantuan Tahap I')
            ->assertSee('1 foto');
        $this->actingAs($admin)->get('/admin/galeri?cari=penyaluran&tahun=2026')->assertOk();
        $this->actingAs($admin)->get('/admin/galeri/tambah')->assertOk();

        // Formulir ubah menampilkan foto yang sudah tersimpan beserta penanda hapusnya.
        $this->actingAs($admin)->get("/admin/galeri/{$galeri->id_galeri}/ubah")->assertOk()
            ->assertSee('Dokumentasi Tersimpan (1)')
            ->assertSee('/storage/galeri/contoh.jpg');
    }

    public function test_pengguna_biasa_ditolak(): void
    {
        $warga = User::create([
            'name'     => 'Warga Galeri',
            'nik'      => '7777777777777777',
            'email'    => 'warga.galeri@example.com',
            'password' => 'rahasia123',
            'role'     => 'user',
        ]);

        $this->actingAs($warga)->get('/admin/galeri')->assertForbidden();
    }

    public function test_tambah_galeri_beserta_foto_dokumentasi(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/galeri', [
            'judul_kegiatan'    => 'Sosialisasi Program PKH 2026',
            'tgl_pelaksanaan'   => '2026-06-15',
            'deskripsi_singkat' => 'Sosialisasi kepada calon penerima manfaat di balai desa.',
            'foto'              => [
                UploadedFile::fake()->image('dokumentasi-1.jpg'),
                UploadedFile::fake()->image('dokumentasi-2.jpg'),
            ],
        ])->assertRedirect(route('admin.galeri.index'));

        $galeri = Galeri::where('judul_kegiatan', 'Sosialisasi Program PKH 2026')->firstOrFail();
        $this->assertSame('sosialisasi-program-pkh-2026', $galeri->slug);
        $this->assertSame('2026-06-15', $galeri->tgl_pelaksanaan->format('Y-m-d'));
        $this->assertCount(2, $galeri->fotos);
        $this->assertSame([1, 2], $galeri->fotos->pluck('urutan')->all());

        foreach ($galeri->fotos as $foto) {
            Storage::disk('public')->assertExists(str_replace('/storage/', '', $foto->path));
        }
    }

    public function test_ubah_galeri_dan_hapus_foto_terpilih(): void
    {
        Storage::fake('public');
        $admin  = $this->admin();
        $galeri = $this->galeri();

        $galeri->fotos()->createMany([
            ['path' => '/storage/galeri/lama-1.jpg', 'urutan' => 1],
            ['path' => '/storage/galeri/lama-2.jpg', 'urutan' => 2],
        ]);
        $dibuang = $galeri->fotos()->first();

        $this->actingAs($admin)->put("/admin/galeri/{$galeri->id_galeri}", [
            'judul_kegiatan'    => 'Penyaluran Bantuan Tahap II',
            'tgl_pelaksanaan'   => '2026-07-01',
            'deskripsi_singkat' => 'Deskripsi diperbarui.',
            'hapus_foto'        => [$dibuang->id_foto],
            'foto'              => [UploadedFile::fake()->image('tambahan.jpg')],
        ])->assertRedirect(route('admin.galeri.index'));

        $galeri->refresh()->load('fotos');
        $this->assertSame('Penyaluran Bantuan Tahap II', $galeri->judul_kegiatan);
        $this->assertSame('penyaluran-bantuan-tahap-ii', $galeri->slug);
        $this->assertCount(2, $galeri->fotos);
        $this->assertNotContains($dibuang->id_foto, $galeri->fotos->pluck('id_foto')->all());
    }

    public function test_hapus_galeri_membuang_foto_terkait(): void
    {
        $admin  = $this->admin();
        $galeri = $this->galeri();
        $galeri->fotos()->create(['path' => '/storage/galeri/satu.jpg', 'urutan' => 1]);

        $this->actingAs($admin)->delete("/admin/galeri/{$galeri->id_galeri}")
            ->assertRedirect(route('admin.galeri.index'));

        $this->assertNull(Galeri::find($galeri->id_galeri));
        $this->assertDatabaseCount('galeri_fotos', 0);
    }

    public function test_validasi_menolak_isian_kosong(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/galeri', [])
            ->assertSessionHasErrors(['judul_kegiatan', 'tgl_pelaksanaan', 'deskripsi_singkat']);
    }

    public function test_foto_galeri_lain_tidak_bisa_dihapus_lewat_galeri_ini(): void
    {
        $admin = $this->admin();
        $satu  = $this->galeri('Kegiatan Satu');
        $dua   = $this->galeri('Kegiatan Dua');

        $fotoDua = $dua->fotos()->create(['path' => '/storage/galeri/milik-dua.jpg', 'urutan' => 1]);

        $this->actingAs($admin)->put("/admin/galeri/{$satu->id_galeri}", [
            'judul_kegiatan'    => 'Kegiatan Satu',
            'tgl_pelaksanaan'   => '2026-05-10',
            'deskripsi_singkat' => 'Tidak berubah.',
            'hapus_foto'        => [$fotoDua->id_foto],
        ])->assertRedirect(route('admin.galeri.index'));

        $this->assertNotNull($dua->fotos()->find($fotoDua->id_foto));
    }

    public function test_halaman_galeri_pengguna_dapat_diakses(): void
    {
        $galeri = $this->galeri();

        $this->get('/galeri')->assertOk()->assertSee('Penyaluran Bantuan Tahap I');
        $this->get('/galeri?cari=penyaluran&tahun=2026')->assertOk();
        $this->get("/galeri/{$galeri->slug}")->assertOk()->assertSee('Media &amp; Dokumentasi', false);
    }
}
