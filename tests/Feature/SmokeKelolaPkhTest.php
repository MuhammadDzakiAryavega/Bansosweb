<?php

namespace Tests\Feature;

use App\Http\Controllers\KelolaPkhController;
use App\Models\Alternatif;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeKelolaPkhTest extends TestCase
{
    use RefreshDatabase;

    private function seksi(): User
    {
        return User::create([
            'name'     => 'Seksi PKH',
            'nik'      => '7777777777777777',
            'email'    => 'seksi.pkh@example.com',
            'password' => 'rahasia123',
            'role'     => 'seksi',
        ]);
    }

    /** Kriteria & sub-kriteria adalah data master, kewenangannya ada di administrator. */
    private function admin(): User
    {
        return User::create([
            'name'     => 'Admin Portal',
            'nik'      => '8888888888888888',
            'email'    => 'admin.pkh@example.com',
            'password' => 'rahasia123',
            'role'     => 'admin',
        ]);
    }

    private function warga(string $nama, string $nik): User
    {
        return User::create([
            'name'     => $nama,
            'nik'      => $nik,
            'email'    => str($nama)->slug() . '@example.com',
            'password' => 'rahasia123',
            'role'     => 'user',
        ]);
    }

    /** Buat dua sub-kriteria (Tinggi=5, Rendah=1) untuk tiap kriteria. */
    private function seedSubKriteria(): array
    {
        $peta = [];
        foreach (array_keys(KelolaPkhController::KRITERIA) as $slug) {
            $peta[$slug] = [
                'tinggi' => SubKriteria::create(['kriteria' => $slug, 'nama' => 'Tinggi', 'nilai' => 5])->id,
                'rendah' => SubKriteria::create(['kriteria' => $slug, 'nama' => 'Rendah', 'nilai' => 1])->id,
            ];
        }

        return $peta;
    }

    public function test_halaman_kriteria_menampilkan_seluruh_kriteria_dalam_satu_halaman(): void
    {
        $admin = $this->admin();
        SubKriteria::create(['kriteria' => 'penghasilan', 'nama' => '< Rp1.000.000', 'nilai' => 5]);
        SubKriteria::create(['kriteria' => 'kepemilikan-aset', 'nama' => 'Tidak Punya Aset', 'nilai' => 5]);

        $halaman = $this->actingAs($admin)->get('/admin/pkh/kriteria')->assertOk()
            ->assertSee('30%')
            ->assertSee('Benefit')
            ->assertSee('&lt; Rp1.000.000', false)
            ->assertSee('Tidak Punya Aset');

        // C1 sampai C5 tampil berurutan pada halaman yang sama.
        foreach (KelolaPkhController::KRITERIA as $kriteria) {
            $halaman->assertSee($kriteria['kode'])->assertSee($kriteria['label']);
        }
    }

    public function test_admin_bisa_menambah_dan_menghapus_sub_kriteria(): void
    {
        $admin = $this->admin();

        // Setelah disimpan, petugas dikembalikan ke bagian kriteria yang diubah.
        $kembali = route('admin.pkh.kriteria') . '#kondisi-rumah';

        $this->actingAs($admin)->post('/admin/pkh/kondisi-rumah/sub', [
            'nama'  => 'Tidak Layak Huni',
            'nilai' => 5,
        ])->assertRedirect($kembali);

        $sub = SubKriteria::where('kriteria', 'kondisi-rumah')->firstOrFail();
        $this->assertSame('Tidak Layak Huni', $sub->nama);
        $this->assertSame(5, $sub->nilai);

        $this->actingAs($admin)->delete("/admin/pkh/sub/{$sub->id}")
            ->assertRedirect($kembali);
        $this->assertNull(SubKriteria::find($sub->id));
    }

    public function test_validasi_sub_kriteria_menolak_isian_kosong(): void
    {
        // Galat masuk ke kantong milik kriteria itu saja, agar pada halaman
        // gabungan tidak ikut muncul di kriteria lain.
        $this->actingAs($this->admin())
            ->post('/admin/pkh/penghasilan/sub', [])
            ->assertSessionHasErrors(['nama', 'nilai'], null, KelolaPkhController::bagKriteria('penghasilan'));
    }

    public function test_seksi_bisa_menilai_calon(): void
    {
        $seksi = $this->seksi();
        $subs = $this->seedSubKriteria();
        $budi = $this->warga('Budi', '1111111111111111');

        // Calon lahir dari verifikasi pendaftaran, bukan input manual di halaman penilaian.
        $alt = Alternatif::create(['user_id' => $budi->id, 'desa' => Pendaftaran::DESA[0]]);

        // Simpan penilaian (pilih sub-kriteria "Tinggi" tiap kriteria).
        $pilihan = [];
        foreach (array_keys(KelolaPkhController::KRITERIA) as $slug) {
            $pilihan[$slug] = $subs[$slug]['tinggi'];
        }

        $this->actingAs($seksi)->put("/admin/pkh/penilaian/{$alt->id}", ['sub' => $pilihan])
            ->assertRedirect(route('admin.pkh.penilaian.index'));

        $this->assertSame(5, Penilaian::where('alternatif_id', $alt->id)->count());
    }

    public function test_perankingan_saw_mengurutkan_dari_skor_tertinggi(): void
    {
        $seksi = $this->seksi();
        $subs = $this->seedSubKriteria();

        $budi = $this->warga('Budi Prioritas', '1111111111111111');   // semua Tinggi → V = 1.0000
        $sari = $this->warga('Sari Rendah', '3333333333333333');       // semua Rendah → V = 0.2000

        $altBudi = Alternatif::create(['user_id' => $budi->id]);
        $altSari = Alternatif::create(['user_id' => $sari->id]);

        foreach (array_keys(KelolaPkhController::KRITERIA) as $slug) {
            Penilaian::create(['alternatif_id' => $altBudi->id, 'kriteria' => $slug, 'sub_kriteria_id' => $subs[$slug]['tinggi']]);
            Penilaian::create(['alternatif_id' => $altSari->id, 'kriteria' => $slug, 'sub_kriteria_id' => $subs[$slug]['rendah']]);
        }

        $this->actingAs($seksi)->get('/admin/pkh/hasil-akhir')->assertOk()
            ->assertSeeInOrder(['Budi Prioritas', 'Sari Rendah'])
            ->assertSee('1.0000')
            ->assertSee('0.2000');
    }

    /**
     * Normalisasi memakai nilai ideal dari data master kriteria, bukan nilai
     * maksimum antar-calon. Calon tunggal karena itu tidak otomatis bernilai
     * 1,0000 — skornya tetap mencerminkan kondisi ekonominya sendiri.
     */
    public function test_calon_tunggal_tidak_otomatis_bernilai_sempurna(): void
    {
        $seksi = $this->seksi();
        $subs = $this->seedSubKriteria();
        $sari = $this->warga('Sari Sendirian', '3333333333333333');
        $alt = Alternatif::create(['user_id' => $sari->id]);

        foreach (array_keys(KelolaPkhController::KRITERIA) as $slug) {
            Penilaian::create(['alternatif_id' => $alt->id, 'kriteria' => $slug, 'sub_kriteria_id' => $subs[$slug]['rendah']]);
        }

        // Nilai 1 dari ideal 5 pada semua kriteria → V = Σ bobot × 0,2 = 0,2000.
        $this->actingAs($seksi)->get('/admin/pkh/hasil-akhir')->assertOk()
            ->assertSee('0.2000')
            ->assertDontSee('1.0000');
    }

    public function test_hasil_dapat_disaring_per_desa(): void
    {
        $seksi = $this->seksi();
        $subs = $this->seedSubKriteria();

        $a = $this->warga('Warga Desa A', '1111111111111111');
        $b = $this->warga('Warga Desa B', '2222222222222222');
        $altA = Alternatif::create(['user_id' => $a->id, 'desa' => Pendaftaran::DESA[0]]);
        $altB = Alternatif::create(['user_id' => $b->id, 'desa' => Pendaftaran::DESA[1]]);

        foreach (array_keys(KelolaPkhController::KRITERIA) as $slug) {
            Penilaian::create(['alternatif_id' => $altA->id, 'kriteria' => $slug, 'sub_kriteria_id' => $subs[$slug]['rendah']]);
            Penilaian::create(['alternatif_id' => $altB->id, 'kriteria' => $slug, 'sub_kriteria_id' => $subs[$slug]['tinggi']]);
        }

        // Tanpa filter: kedua desa tampil.
        $this->actingAs($seksi)->get('/admin/pkh/hasil-akhir')->assertOk()
            ->assertSee('Warga Desa A')->assertSee('Warga Desa B');

        // Filter satu desa: hanya calon desa itu yang diperingkat, dan skornya
        // tidak ikut berubah meski ia menjadi satu-satunya calon yang tampil.
        $this->actingAs($seksi)->get('/admin/pkh/hasil-akhir?desa=' . urlencode(Pendaftaran::DESA[0]))->assertOk()
            ->assertSee('Warga Desa A')->assertDontSee('Warga Desa B')
            ->assertSee('0.2000');
    }

    public function test_calon_belum_lengkap_dikecualikan_dari_ranking(): void
    {
        $seksi = $this->seksi();
        $subs = $this->seedSubKriteria();
        $andi = $this->warga('Andi Belum', '4444444444444444');
        $alt = Alternatif::create(['user_id' => $andi->id]);

        // Hanya menilai 2 dari 5 kriteria.
        Penilaian::create(['alternatif_id' => $alt->id, 'kriteria' => 'penghasilan', 'sub_kriteria_id' => $subs['penghasilan']['tinggi']]);
        Penilaian::create(['alternatif_id' => $alt->id, 'kriteria' => 'kondisi-rumah', 'sub_kriteria_id' => $subs['kondisi-rumah']['tinggi']]);

        $this->actingAs($seksi)->get('/admin/pkh/hasil-akhir')->assertOk()
            ->assertSee('belum dinilai lengkap')
            ->assertSee('Andi Belum');
    }

    public function test_semua_halaman_pkh_dapat_diakses_seksi(): void
    {
        $seksi = $this->seksi();

        $this->actingAs($seksi)->get('/admin/pkh/penilaian')->assertOk()->assertSee('Penilaian');
        $this->actingAs($seksi)->get('/admin/pkh/hasil-akhir')->assertOk()->assertSee('Hasil Akhir');
        $this->actingAs($this->admin())->get('/admin/pkh/kriteria')->assertOk()->assertSee('Kelola Kriteria');
    }

    /** Kriteria milik admin: seksi cukup memakai nilainya, tidak mengubah master. */
    public function test_seksi_tidak_dapat_mengubah_data_master_kriteria(): void
    {
        $seksi = $this->seksi();

        $this->actingAs($seksi)->get('/admin/pkh/kriteria')->assertForbidden();
        $this->actingAs($seksi)->post('/admin/pkh/penghasilan/sub', ['nama' => 'Coba', 'nilai' => 3])
            ->assertForbidden();
        $this->assertSame(0, SubKriteria::count());
    }

    public function test_kriteria_tidak_dikenal_menghasilkan_404(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/pkh/kriteria-ngawur/sub', ['nama' => 'Ngawur', 'nilai' => 3])
            ->assertNotFound();
    }

    public function test_pengguna_biasa_ditolak(): void
    {
        $warga = $this->warga('Warga Biasa', '6666666666666666');

        $this->actingAs($warga)->get('/admin/pkh/kriteria')->assertForbidden();
        $this->actingAs($warga)->get('/admin/pkh/penilaian')->assertForbidden();
        $this->actingAs($warga)->get('/admin/pkh/hasil-akhir')->assertForbidden();
    }

    public function test_sisi_pengguna_tidak_lagi_menyebut_cek_bansos(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Cek Bansos');
    }
}
