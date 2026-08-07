<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\KelolaArsipController;
use App\Http\Controllers\KelolaBeritaController;
use App\Http\Controllers\KelolaGaleriController;
use App\Http\Controllers\KelolaPendaftaranController;
use App\Http\Controllers\KelolaPengaduanController;
use App\Http\Controllers\KelolaPkhController;
use App\Http\Controllers\KelolaPkhPenilaianController;
use App\Http\Controllers\KelolaUserController;
use App\Http\Controllers\PendaftaranPkhController;
use App\Http\Controllers\PengaduanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Portal PKH
|--------------------------------------------------------------------------
*/

// --- HALAMAN PUBLIK ---
Route::get('/', function () {
    return view('Pengguna.HalamanUtama.home');
})->name('home');

Route::get('/tentang', function () {
    return view('Pengguna.tentangs.tentang');
})->name('tentang');

// Berita (publik, hanya yang berstatus Published)
Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{berita:slug}', [BeritaController::class, 'show'])->name('berita.show');

// Galeri kegiatan (publik)
Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri.index');
Route::get('/galeri/{galeri:slug}', [GaleriController::class, 'show'])->name('galeri.show');

// --- AUTENTIKASI (hanya untuk tamu / belum login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// --- HANYA UNTUK YANG SUDAH LOGIN ---
Route::middleware('auth')->group(function () {
    // Dashboard & Logout
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Pengaduan (wajib login)
    Route::get('/pengaduan', [PengaduanController::class, 'create'])->name('pengaduan');
    Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
    Route::get('/pengaduan/riwayat', [PengaduanController::class, 'riwayat'])->name('pengaduan.riwayat');

    // Pendaftaran PKH (wajib login)
    Route::get('/pendaftaran-pkh', [PendaftaranPkhController::class, 'create'])->name('pkh.daftar');
    Route::post('/pendaftaran-pkh', [PendaftaranPkhController::class, 'store'])->name('pkh.daftar.store');
});

/*
| --- AREA PANEL PETUGAS ---
|
| Kewenangan dipisah tegas antara dua peran dan tidak saling tumpang tindih:
|   - admin : konten portal (berita, galeri) & akun pengguna
|   - seksi : proses PKH (SPK-SAW), pengaduan masyarakat, dan arsip dokumen
|
| Pemisahan ini ditegakkan di sini, bukan hanya disembunyikan dari menu, agar
| akses langsung lewat URL pun tetap ditolak dengan 403.
*/
Route::middleware(['auth', 'role:admin,seksi'])->prefix('admin')->name('admin.')->group(function () {

    /* ============ KEWENANGAN ADMINISTRATOR ============ */
    Route::middleware('role:admin')->group(function () {
        // Kelola berita
        Route::get('/berita', [KelolaBeritaController::class, 'index'])->name('berita.index');
        Route::get('/berita/tambah', [KelolaBeritaController::class, 'create'])->name('berita.create');
        Route::post('/berita', [KelolaBeritaController::class, 'store'])->name('berita.store');
        Route::get('/berita/{berita}/ubah', [KelolaBeritaController::class, 'edit'])->name('berita.edit');
        Route::put('/berita/{berita}', [KelolaBeritaController::class, 'update'])->name('berita.update');
        Route::delete('/berita/{berita}', [KelolaBeritaController::class, 'destroy'])->name('berita.destroy');

        // Kelola galeri
        Route::get('/galeri', [KelolaGaleriController::class, 'index'])->name('galeri.index');
        Route::get('/galeri/tambah', [KelolaGaleriController::class, 'create'])->name('galeri.create');
        Route::post('/galeri', [KelolaGaleriController::class, 'store'])->name('galeri.store');
        Route::get('/galeri/{galeri}/ubah', [KelolaGaleriController::class, 'edit'])->name('galeri.edit');
        Route::put('/galeri/{galeri}', [KelolaGaleriController::class, 'update'])->name('galeri.update');
        Route::delete('/galeri/{galeri}', [KelolaGaleriController::class, 'destroy'])->name('galeri.destroy');

        // Kelola kriteria PKH — data master C1–C5 beserta sub-kriterianya,
        // seluruhnya pada satu halaman. Seksi memakai nilainya saat menilai,
        // tetapi yang berwenang mengubah master hanyalah administrator.
        Route::prefix('pkh')->name('pkh.')->group(function () {
            Route::get('/kriteria', [KelolaPkhController::class, 'kriteria'])->name('kriteria');

            Route::post('/{kriteria}/sub', [KelolaPkhController::class, 'storeSub'])
                ->whereIn('kriteria', array_keys(KelolaPkhController::KRITERIA))->name('sub.store');
            Route::put('/sub/{sub}', [KelolaPkhController::class, 'updateSub'])->name('sub.update');
            Route::delete('/sub/{sub}', [KelolaPkhController::class, 'destroySub'])->name('sub.destroy');
        });

        // Kelola pengguna (termasuk membuat akun seksi)
        Route::get('/pengguna', [KelolaUserController::class, 'index'])->name('user.index');
        Route::get('/pengguna/tambah', [KelolaUserController::class, 'create'])->name('user.create');
        Route::post('/pengguna', [KelolaUserController::class, 'store'])->name('user.store');
        Route::get('/pengguna/{user}/ubah', [KelolaUserController::class, 'edit'])->name('user.edit');
        Route::put('/pengguna/{user}', [KelolaUserController::class, 'update'])->name('user.update');
        Route::delete('/pengguna/{user}', [KelolaUserController::class, 'destroy'])->name('user.destroy');
    });

    /* ============ KEWENANGAN SEKSI ============ */
    Route::middleware('role:seksi')->group(function () {
        // Kelola arsip (dokumen internal)
        Route::get('/arsip', [KelolaArsipController::class, 'index'])->name('arsip.index');
        Route::get('/arsip/tambah', [KelolaArsipController::class, 'create'])->name('arsip.create');
        Route::post('/arsip', [KelolaArsipController::class, 'store'])->name('arsip.store');
        Route::get('/arsip/{arsip}/ubah', [KelolaArsipController::class, 'edit'])->name('arsip.edit');
        Route::get('/arsip/{arsip}/lampiran', [KelolaArsipController::class, 'unduh'])->name('arsip.unduh');
        Route::put('/arsip/{arsip}', [KelolaArsipController::class, 'update'])->name('arsip.update');
        Route::delete('/arsip/{arsip}', [KelolaArsipController::class, 'destroy'])->name('arsip.destroy');

        // Kelola PKH — Sistem Pendukung Keputusan metode SAW
        Route::prefix('pkh')->name('pkh.')->group(function () {
            // Pendaftaran calon (pengajuan warga yang ditinjau seksi)
            Route::get('/pendaftaran', [KelolaPendaftaranController::class, 'index'])->name('pendaftaran.index');
            Route::get('/pendaftaran/{pendaftaran}', [KelolaPendaftaranController::class, 'show'])->name('pendaftaran.show');
            Route::get('/pendaftaran/{pendaftaran}/foto/{jenis}', [KelolaPendaftaranController::class, 'foto'])->name('pendaftaran.foto');
            Route::post('/pendaftaran/{pendaftaran}/verifikasi', [KelolaPendaftaranController::class, 'verifikasi'])->name('pendaftaran.verifikasi');
            Route::post('/pendaftaran/{pendaftaran}/tolak', [KelolaPendaftaranController::class, 'tolak'])->name('pendaftaran.tolak');
            Route::delete('/pendaftaran/{pendaftaran}', [KelolaPendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

            // Penilaian calon penerima (alternatif lahir dari verifikasi pendaftaran)
            Route::get('/penilaian', [KelolaPkhPenilaianController::class, 'index'])->name('penilaian.index');
            Route::get('/penilaian/{alternatif}/nilai', [KelolaPkhPenilaianController::class, 'edit'])->name('penilaian.edit');
            Route::put('/penilaian/{alternatif}', [KelolaPkhPenilaianController::class, 'update'])->name('penilaian.update');
            Route::delete('/penilaian/{alternatif}', [KelolaPkhPenilaianController::class, 'destroy'])->name('penilaian.destroy');

            // Hasil akhir perankingan SAW
            Route::get('/hasil-akhir', [KelolaPkhController::class, 'hasil'])->name('hasil');
            Route::get('/hasil-akhir/laporan', [KelolaPkhController::class, 'laporan'])->name('hasil.laporan');
        });

        // Kelola pengaduan
        Route::get('/pengaduan', [KelolaPengaduanController::class, 'index'])->name('pengaduan.index');
        Route::get('/pengaduan/{pengaduan}', [KelolaPengaduanController::class, 'show'])->name('pengaduan.show');
        Route::patch('/pengaduan/{pengaduan}/status', [KelolaPengaduanController::class, 'updateStatus'])->name('pengaduan.updateStatus');
        Route::delete('/pengaduan/{pengaduan}', [KelolaPengaduanController::class, 'destroy'])->name('pengaduan.destroy');
    });
});