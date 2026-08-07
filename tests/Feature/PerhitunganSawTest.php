<?php

namespace Tests\Feature;

use App\Http\Controllers\KelolaPkhController;
use App\Models\Pendaftaran;
use App\Models\User;
use Database\Seeders\PkhDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

/**
 * Mengunci contoh perhitungan SAW pada ARAHAN_BAB_3 Bagian 5.8: bila data demo
 * berubah atau rumus normalisasi tersentuh, angka pada naskah TA tidak boleh
 * ikut bergeser tanpa disadari.
 */
class PerhitunganSawTest extends TestCase
{
    use RefreshDatabase;

    private function seksi(): User
    {
        $this->seed(PkhDemoSeeder::class);

        return User::where('email', 'seksi@pkh.test')->firstOrFail();
    }

    public function test_perankingan_data_demo_sesuai_contoh_bab_3(): void
    {
        $halaman = $this->actingAs($this->seksi())->get('/admin/pkh/hasil-akhir')->assertOk();

        $halaman->assertSeeInOrder([
            'Sarni', 'Rahmat Hidayat', 'Joko Susilo', 'Siti Aminah',
            'Yanti Marlina', 'Budi Hartono', 'Wati Lestari',
        ]);

        foreach (['1.0000', '0.9000', '0.8400', '0.8200', '0.7400', '0.4600', '0.3100'] as $skor) {
            $halaman->assertSee($skor);
        }

        // Dedi hanya dinilai 3 dari 5 kriteria sehingga tidak ikut diperingkat.
        $halaman->assertSee('belum dinilai lengkap')->assertSee('Dedi Kurniawan');
    }

    /**
     * Pembagi normalisasi berasal dari nilai ideal data master, sehingga
     * penyaringan desa hanya membatasi peserta perankingan — skor tiap calon
     * tetap sama dengan mode gabungan.
     */
    public function test_penyaringan_desa_tidak_mengubah_nilai_preferensi(): void
    {
        $this->actingAs($this->seksi())
            ->get('/admin/pkh/hasil-akhir?desa=' . urlencode('Pasar Bantal'))
            ->assertOk()
            ->assertSeeInOrder(['Sarni', 'Siti Aminah', 'Budi Hartono'])
            ->assertSee('1.0000')   // Sarni, sama seperti mode gabungan
            ->assertSee('0.8200')   // Siti Aminah
            ->assertSee('0.4600')   // Budi Hartono
            ->assertDontSee('Joko Susilo');
    }

    /** Desa pada konstanta tetap menjadi acuan penyaring. */
    public function test_desa_pasar_bantal_terdaftar_pada_konstanta(): void
    {
        $this->assertContains('Pasar Bantal', Pendaftaran::DESA);
    }

    /** Skor di bawah ambang 0,65 ditandai tidak layak pada halaman hasil akhir. */
    public function test_skor_di_bawah_ambang_ditandai_tidak_layak(): void
    {
        $this->assertSame(0.65, KelolaPkhController::AMBANG_KELAYAKAN);

        // Data demo: 0,4600 (Budi Hartono) dan 0,3100 (Wati Lestari) di bawah ambang.
        $this->actingAs($this->seksi())
            ->get('/admin/pkh/hasil-akhir')
            ->assertOk()
            ->assertSee('Tidak Layak')
            ->assertSee('5 layak')
            ->assertSee('2 tidak layak');
    }

    /** Laporan Excel memuat keputusan layak/tidak layak untuk tiap calon. */
    public function test_laporan_excel_memuat_keputusan_kelayakan(): void
    {
        $response = $this->actingAs($this->seksi())->get('/admin/pkh/hasil-akhir/laporan');

        $response->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $berkas = $response->baseResponse->getFile()->getPathname();

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($berkas) === true, 'Berkas laporan bukan paket xlsx yang sah.');
        $lembar = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        @unlink($berkas);

        $this->assertNotFalse($lembar);
        $this->assertNotFalse(simplexml_load_string($lembar), 'Lembar kerja bukan XML yang sah.');

        // Dua calon di bawah ambang, lima sisanya layak.
        $this->assertSame(2, substr_count($lembar, 'TIDAK LAYAK'));
        $this->assertSame(5, substr_count($lembar, '>LAYAK<'));

        $this->assertStringContainsString('Sarni', $lembar);
        $this->assertStringContainsString('LAPORAN HASIL AKHIR SELEKSI PENERIMA PKH', $lembar);
        $this->assertStringContainsString('<v>0.31</v>', $lembar);
    }
}
