<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SmokePendaftaranPkhTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** Foto wajib: 4 bukti kondisi rumah + foto diri memegang KTP. */
    private function fotoValid(): array
    {
        return [
            'foto_depan'      => UploadedFile::fake()->image('depan.jpg'),
            'foto_belakang'   => UploadedFile::fake()->image('belakang.jpg'),
            'foto_ruang_tamu' => UploadedFile::fake()->image('ruang-tamu.jpg'),
            'foto_wc'         => UploadedFile::fake()->image('wc.jpg'),
            'foto_ktp'        => UploadedFile::fake()->image('ktp.jpg'),
        ];
    }

    private function seksi(): User
    {
        return User::create([
            'name' => 'Seksi', 'nik' => '9000000000000000',
            'email' => 'seksi@example.com', 'password' => 'rahasia123', 'role' => 'seksi',
        ]);
    }

    private function warga(string $nama = 'Warga', string $nik = '1000000000000001'): User
    {
        return User::create([
            'name' => $nama, 'nik' => $nik,
            'email' => str($nama)->slug() . '@example.com', 'password' => 'rahasia123', 'role' => 'user',
        ]);
    }

    private function isianValid(array $ubahan = []): array
    {
        return array_merge([
            'nama'              => 'Budi Santoso',
            'nik'               => '1000000000000001',
            'desa'              => Pendaftaran::DESA[0],
            'alamat'            => 'Dusun II, RT 003 / RW 001',
            'no_hp'             => '081234567890',
            'penghasilan'       => Pendaftaran::PENGHASILAN[0],
            'jumlah_tanggungan' => 3,
            'kondisi_rumah'     => Pendaftaran::KONDISI_RUMAH[0],
            'status_pekerjaan'  => Pendaftaran::STATUS_PEKERJAAN[0],
            'kepemilikan_aset'  => Pendaftaran::KEPEMILIKAN_ASET[0],
        ], $ubahan);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/pendaftaran-pkh')->assertRedirect(route('login'));
    }

    public function test_warga_dapat_membuka_dan_mengirim_pendaftaran(): void
    {
        $warga = $this->warga();

        $this->actingAs($warga)->get('/pendaftaran-pkh')->assertOk()->assertSee('Pendaftaran PKH');

        $this->actingAs($warga)->post('/pendaftaran-pkh', $this->isianValid() + $this->fotoValid())
            ->assertRedirect(route('pkh.daftar'))
            ->assertSessionHas('status', 'pkh-terdaftar');

        $daftar = Pendaftaran::where('user_id', $warga->id)->firstOrFail();
        $this->assertSame('Baru', $daftar->status);
        $this->assertSame(3, $daftar->jumlah_tanggungan);

        // Seluruh foto (rumah + KTP) tersimpan pada disk privat.
        foreach (array_keys(Pendaftaran::jenisFoto()) as $field) {
            $this->assertNotNull($daftar->{$field});
            Storage::disk('local')->assertExists($daftar->{$field});
        }
    }

    public function test_pendaftaran_wajib_menyertakan_semua_foto(): void
    {
        $warga = $this->warga();

        $this->actingAs($warga)->post('/pendaftaran-pkh', $this->isianValid())
            ->assertSessionHasErrors(['foto_depan', 'foto_belakang', 'foto_ruang_tamu', 'foto_wc', 'foto_ktp']);

        $this->assertSame(0, Pendaftaran::count());
    }

    public function test_foto_hanya_dapat_dilihat_petugas_seksi(): void
    {
        $seksi = $this->seksi();
        $warga = $this->warga();

        $this->actingAs($warga)->post('/pendaftaran-pkh', $this->isianValid() + $this->fotoValid());
        $daftar = Pendaftaran::where('user_id', $warga->id)->firstOrFail();

        // Admin dapat membuka foto rumah maupun foto identitas; jenis asing → 404.
        $this->actingAs($seksi)->get("/admin/pkh/pendaftaran/{$daftar->id}/foto/foto_depan")->assertOk();
        $this->actingAs($seksi)->get("/admin/pkh/pendaftaran/{$daftar->id}/foto/foto_ktp")->assertOk();
        $this->actingAs($seksi)->get("/admin/pkh/pendaftaran/{$daftar->id}/foto/foto_ngawur")->assertNotFound();

        // Warga biasa tidak boleh mengakses foto lewat area admin.
        $this->actingAs($warga)->get("/admin/pkh/pendaftaran/{$daftar->id}/foto/foto_depan")->assertForbidden();
    }

    public function test_validasi_menolak_isian_kosong_dan_pilihan_tidak_valid(): void
    {
        $warga = $this->warga();

        $this->actingAs($warga)->post('/pendaftaran-pkh', [])
            ->assertSessionHasErrors(['nama', 'nik', 'desa', 'alamat', 'penghasilan', 'jumlah_tanggungan', 'kondisi_rumah', 'status_pekerjaan', 'kepemilikan_aset']);

        $this->actingAs($warga)->post('/pendaftaran-pkh', $this->isianValid(['penghasilan' => 'Ngawur']))
            ->assertSessionHasErrors('penghasilan');
    }

    public function test_pengajuan_aktif_tidak_dapat_digandakan(): void
    {
        $warga = $this->warga();
        Pendaftaran::create($this->isianValid() + ['user_id' => $warga->id, 'status' => 'Baru']);

        $this->actingAs($warga)->post('/pendaftaran-pkh', $this->isianValid())
            ->assertRedirect(route('pkh.daftar'))
            ->assertSessionHas('error');

        $this->assertSame(1, Pendaftaran::where('user_id', $warga->id)->count());
    }

    public function test_seksi_dapat_meninjau_daftar_dan_detail(): void
    {
        $seksi = $this->seksi();
        $warga = $this->warga();
        $daftar = Pendaftaran::create($this->isianValid() + ['user_id' => $warga->id, 'status' => 'Baru']);

        $this->actingAs($seksi)->get('/admin/pkh/pendaftaran')->assertOk()->assertSee('Budi Santoso');
        $this->actingAs($seksi)->get("/admin/pkh/pendaftaran/{$daftar->id}")->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee(Pendaftaran::KONDISI_RUMAH[0]);
    }

    public function test_verifikasi_menjadikan_pengaju_sebagai_calon(): void
    {
        $seksi = $this->seksi();
        $warga = $this->warga();
        $daftar = Pendaftaran::create($this->isianValid() + ['user_id' => $warga->id, 'status' => 'Baru']);

        $this->actingAs($seksi)->post("/admin/pkh/pendaftaran/{$daftar->id}/verifikasi")
            ->assertRedirect(route('admin.pkh.pendaftaran.show', $daftar));

        $this->assertSame('Diverifikasi', $daftar->fresh()->status);

        // Calon terbentuk & desanya disalin dari pendaftaran.
        $alt = Alternatif::where('user_id', $warga->id)->firstOrFail();
        $this->assertSame($daftar->desa, $alt->desa);
    }

    public function test_penolakan_menyimpan_status_dan_catatan(): void
    {
        $seksi = $this->seksi();
        $warga = $this->warga();
        $daftar = Pendaftaran::create($this->isianValid() + ['user_id' => $warga->id, 'status' => 'Baru']);

        $this->actingAs($seksi)->post("/admin/pkh/pendaftaran/{$daftar->id}/tolak", [
            'catatan_admin' => 'Data tidak sesuai verifikasi lapangan.',
        ])->assertRedirect(route('admin.pkh.pendaftaran.show', $daftar));

        $daftar->refresh();
        $this->assertSame('Ditolak', $daftar->status);
        $this->assertSame('Data tidak sesuai verifikasi lapangan.', $daftar->catatan_admin);
        $this->assertFalse(Alternatif::where('user_id', $warga->id)->exists());
    }

    public function test_penilaian_calon_menampilkan_acuan_dan_foto_pendaftaran(): void
    {
        $seksi = $this->seksi();
        $warga = $this->warga();

        $this->actingAs($warga)->post('/pendaftaran-pkh', $this->isianValid() + $this->fotoValid());
        $daftar = Pendaftaran::where('user_id', $warga->id)->firstOrFail();

        $this->actingAs($seksi)->post("/admin/pkh/pendaftaran/{$daftar->id}/verifikasi");
        $alt = Alternatif::where('user_id', $warga->id)->firstOrFail();

        $this->actingAs($seksi)->get("/admin/pkh/penilaian/{$alt->id}/nilai")->assertOk()
            ->assertSee('Acuan Data Pendaftaran')
            ->assertSee($daftar->kondisi_rumah)
            ->assertSee("/admin/pkh/pendaftaran/{$daftar->id}/foto/foto_depan");
    }

    public function test_warga_tidak_bisa_membuka_area_admin_pendaftaran(): void
    {
        $warga = $this->warga();
        $daftar = Pendaftaran::create($this->isianValid() + ['user_id' => $warga->id, 'status' => 'Baru']);

        $this->actingAs($warga)->get('/admin/pkh/pendaftaran')->assertForbidden();
        $this->actingAs($warga)->post("/admin/pkh/pendaftaran/{$daftar->id}/verifikasi")->assertForbidden();
    }

    public function test_beranda_menampilkan_ajakan_daftar_pkh(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Daftar PKH')
            ->assertSee('Pendaftaran PKH');
    }
}
