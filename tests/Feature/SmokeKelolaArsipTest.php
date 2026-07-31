<?php

namespace Tests\Feature;

use App\Models\Arsip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SmokeKelolaArsipTest extends TestCase
{
    use RefreshDatabase;

    private function seksi(): User
    {
        return User::create([
            'name'     => 'Seksi Arsip',
            'nik'      => '8888888888888888',
            'email'    => 'seksi.arsip@example.com',
            'password' => 'rahasia123',
            'role'     => 'seksi',
        ]);
    }

    private function arsip(array $ubahan = []): Arsip
    {
        return Arsip::create(array_merge([
            'nomor_arsip'        => 'ARS/001/V/2026',
            'tgl_dokumen'        => '2026-05-10',
            'judul_arsip'        => 'Surat Undangan Rapat Koordinasi PKH',
            'klasifikasi'        => 'Surat Masuk',
            'deskripsi_tambahan' => 'Undangan dari Dinas Sosial Provinsi Bengkulu.',
            'status_publikasi'   => 'Published',
            'tanggal_publikasi'  => now(),
        ], $ubahan));
    }

    public function test_halaman_kelola_arsip_dapat_diakses(): void
    {
        $seksi = $this->seksi();
        $arsip = $this->arsip();

        $this->actingAs($seksi)->get('/admin/arsip')->assertOk()
            ->assertSee('ARS/001/V/2026')
            ->assertSee('Surat Undangan Rapat Koordinasi PKH')
            ->assertSee('Surat Masuk');

        $this->actingAs($seksi)->get('/admin/arsip?cari=undangan&klasifikasi=Surat+Masuk&status=Published&tahun=2026')
            ->assertOk()->assertSee('ARS/001/V/2026');

        // Nomor usulan otomatis muncul pada formulir tambah.
        $this->actingAs($seksi)->get('/admin/arsip/tambah')->assertOk()
            ->assertSee('Lainnya (buat klasifikasi baru)');

        $this->actingAs($seksi)->get("/admin/arsip/{$arsip->id_arsip}/ubah")->assertOk()
            ->assertSee('ARS/001/V/2026');
    }

    public function test_pengguna_biasa_ditolak(): void
    {
        $warga = User::create([
            'name'     => 'Warga Arsip',
            'nik'      => '9999999999999999',
            'email'    => 'warga.arsip@example.com',
            'password' => 'rahasia123',
            'role'     => 'user',
        ]);

        $arsip = $this->arsip();

        $this->actingAs($warga)->get('/admin/arsip')->assertForbidden();
        $this->actingAs($warga)->get("/admin/arsip/{$arsip->id_arsip}/lampiran")->assertForbidden();
    }

    public function test_tambah_arsip_beserta_lampiran(): void
    {
        Storage::fake('local');
        $seksi = $this->seksi();

        $this->actingAs($seksi)->post('/admin/arsip', [
            'nomor_arsip'        => 'ARS/002/VI/2026',
            'tgl_dokumen'        => '2026-06-15',
            'judul_arsip'        => 'Laporan Penyaluran Bantuan Tahap II',
            'klasifikasi'        => 'Dokumen Penting',
            'deskripsi_tambahan' => 'Rekapitulasi penyaluran per kecamatan.',
            'lampiran'           => UploadedFile::fake()->create('laporan-tahap-2.pdf', 120, 'application/pdf'),
            'status_publikasi'   => 'Published',
        ])->assertRedirect(route('admin.arsip.index'));

        $arsip = Arsip::where('nomor_arsip', 'ARS/002/VI/2026')->firstOrFail();
        $this->assertSame('Dokumen Penting', $arsip->klasifikasi);
        $this->assertSame('2026-06-15', $arsip->tgl_dokumen->format('Y-m-d'));
        $this->assertSame('laporan-tahap-2.pdf', $arsip->lampiran_nama);
        $this->assertNotNull($arsip->tanggal_publikasi);
        Storage::disk('local')->assertExists($arsip->lampiran);

        // Lampiran hanya bisa diunduh lewat rute admin.
        $this->actingAs($seksi)->get("/admin/arsip/{$arsip->id_arsip}/lampiran")
            ->assertOk()
            ->assertDownload('laporan-tahap-2.pdf');
    }

    public function test_klasifikasi_baru_bisa_dibuat_dan_ikut_jadi_pilihan(): void
    {
        $seksi = $this->seksi();

        $this->actingAs($seksi)->post('/admin/arsip', [
            'nomor_arsip'      => 'ARS/003/VII/2026',
            'tgl_dokumen'      => '2026-07-01',
            'judul_arsip'      => 'Nota Dinas Penugasan Pendamping',
            'klasifikasi'      => '__baru__',
            'klasifikasi_baru' => 'Nota Dinas',
            'status_publikasi' => 'Draft',
        ])->assertRedirect(route('admin.arsip.index'));

        $arsip = Arsip::where('nomor_arsip', 'ARS/003/VII/2026')->firstOrFail();
        $this->assertSame('Nota Dinas', $arsip->klasifikasi);
        $this->assertNull($arsip->tanggal_publikasi);
        $this->assertContains('Nota Dinas', Arsip::daftarKlasifikasi());

        // Klasifikasi buatan petugas tersedia pada saringan daftar & formulir berikutnya.
        $this->actingAs($seksi)->get('/admin/arsip')->assertOk()->assertSee('Nota Dinas');
    }

    public function test_klasifikasi_baru_wajib_diisi_saat_memilih_lainnya(): void
    {
        $seksi = $this->seksi();

        $this->actingAs($seksi)->post('/admin/arsip', [
            'nomor_arsip'      => 'ARS/004/VII/2026',
            'tgl_dokumen'      => '2026-07-02',
            'judul_arsip'      => 'Dokumen Tanpa Klasifikasi',
            'klasifikasi'      => '__baru__',
            'status_publikasi' => 'Draft',
        ])->assertSessionHasErrors('klasifikasi_baru');
    }

    public function test_ubah_arsip_mengganti_lampiran_lama(): void
    {
        Storage::fake('local');
        $seksi = $this->seksi();

        $lama = UploadedFile::fake()->create('lama.pdf', 50, 'application/pdf')->store('arsip', 'local');
        $arsip = $this->arsip([
            'lampiran'        => $lama,
            'lampiran_nama'   => 'lama.pdf',
            'lampiran_ukuran' => 51200,
        ]);

        $this->actingAs($seksi)->put("/admin/arsip/{$arsip->id_arsip}", [
            'nomor_arsip'      => 'ARS/001/V/2026',
            'tgl_dokumen'      => '2026-05-12',
            'judul_arsip'      => 'Surat Undangan Rapat Koordinasi PKH (Revisi)',
            'klasifikasi'      => 'Surat Masuk',
            'lampiran'         => UploadedFile::fake()->create('baru.pdf', 80, 'application/pdf'),
            'status_publikasi' => 'Published',
        ])->assertRedirect(route('admin.arsip.index'));

        $arsip->refresh();
        $this->assertSame('baru.pdf', $arsip->lampiran_nama);
        Storage::disk('local')->assertMissing($lama);
        Storage::disk('local')->assertExists($arsip->lampiran);
    }

    public function test_lampiran_bisa_dihapus_tanpa_menghapus_arsip(): void
    {
        Storage::fake('local');
        $seksi = $this->seksi();

        $berkas = UploadedFile::fake()->create('dibuang.pdf', 30, 'application/pdf')->store('arsip', 'local');
        $arsip = $this->arsip([
            'lampiran'        => $berkas,
            'lampiran_nama'   => 'dibuang.pdf',
            'lampiran_ukuran' => 30720,
        ]);

        $this->actingAs($seksi)->put("/admin/arsip/{$arsip->id_arsip}", [
            'nomor_arsip'      => 'ARS/001/V/2026',
            'tgl_dokumen'      => '2026-05-10',
            'judul_arsip'      => 'Surat Undangan Rapat Koordinasi PKH',
            'klasifikasi'      => 'Surat Masuk',
            'hapus_lampiran'   => '1',
            'status_publikasi' => 'Published',
        ])->assertRedirect(route('admin.arsip.index'));

        $arsip->refresh();
        $this->assertFalse($arsip->adaLampiran());
        $this->assertNull($arsip->lampiran_nama);
        Storage::disk('local')->assertMissing($berkas);
        $this->assertNotNull(Arsip::find($arsip->id_arsip));
    }

    public function test_hapus_arsip_membuang_lampirannya(): void
    {
        Storage::fake('local');
        $seksi = $this->seksi();

        $berkas = UploadedFile::fake()->create('arsip.pdf', 40, 'application/pdf')->store('arsip', 'local');
        $arsip = $this->arsip([
            'lampiran'        => $berkas,
            'lampiran_nama'   => 'arsip.pdf',
            'lampiran_ukuran' => 40960,
        ]);

        $this->actingAs($seksi)->delete("/admin/arsip/{$arsip->id_arsip}")
            ->assertRedirect(route('admin.arsip.index'));

        $this->assertNull(Arsip::find($arsip->id_arsip));
        Storage::disk('local')->assertMissing($berkas);
    }

    public function test_validasi_menolak_isian_kosong_dan_nomor_ganda(): void
    {
        $seksi = $this->seksi();
        $this->arsip();

        $this->actingAs($seksi)->post('/admin/arsip', [])
            ->assertSessionHasErrors(['nomor_arsip', 'tgl_dokumen', 'judul_arsip', 'klasifikasi', 'status_publikasi']);

        $this->actingAs($seksi)->post('/admin/arsip', [
            'nomor_arsip'      => 'ARS/001/V/2026',
            'tgl_dokumen'      => '2026-05-20',
            'judul_arsip'      => 'Dokumen dengan nomor kembar',
            'klasifikasi'      => 'Surat Keluar',
            'status_publikasi' => 'Draft',
        ])->assertSessionHasErrors('nomor_arsip');
    }

    public function test_nomor_arsip_berikutnya_mengikuti_pola_penomoran(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 5));

        $this->assertSame('ARS/001/X/2026', Arsip::nomorBerikutnya());

        $this->arsip(['nomor_arsip' => 'ARS/001/X/2026', 'tgl_dokumen' => '2026-10-01']);

        $this->assertSame('ARS/002/X/2026', Arsip::nomorBerikutnya());
    }
}
