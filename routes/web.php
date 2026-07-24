<?php

use App\Http\Controllers\Admin\ArsipController as AdminArsipController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;
use App\Http\Controllers\Admin\PengaduanController as AdminPengaduanController;
use App\Http\Controllers\Admin\PkhController as AdminPkhController;
use App\Http\Controllers\Admin\PkhPenilaianController as AdminPkhPenilaianController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\GaleriController;
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

// --- AREA ADMIN (hanya untuk role admin) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Kelola berita
    Route::get('/berita', [AdminBeritaController::class, 'index'])->name('berita.index');
    Route::get('/berita/tambah', [AdminBeritaController::class, 'create'])->name('berita.create');
    Route::post('/berita', [AdminBeritaController::class, 'store'])->name('berita.store');
    Route::get('/berita/{berita}/ubah', [AdminBeritaController::class, 'edit'])->name('berita.edit');
    Route::put('/berita/{berita}', [AdminBeritaController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{berita}', [AdminBeritaController::class, 'destroy'])->name('berita.destroy');

    // Kelola galeri
    Route::get('/galeri', [AdminGaleriController::class, 'index'])->name('galeri.index');
    Route::get('/galeri/tambah', [AdminGaleriController::class, 'create'])->name('galeri.create');
    Route::post('/galeri', [AdminGaleriController::class, 'store'])->name('galeri.store');
    Route::get('/galeri/{galeri}/ubah', [AdminGaleriController::class, 'edit'])->name('galeri.edit');
    Route::put('/galeri/{galeri}', [AdminGaleriController::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/{galeri}', [AdminGaleriController::class, 'destroy'])->name('galeri.destroy');

    // Kelola pengguna
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('user.index');
    Route::get('/pengguna/tambah', [AdminUserController::class, 'create'])->name('user.create');
    Route::post('/pengguna', [AdminUserController::class, 'store'])->name('user.store');
    Route::get('/pengguna/{user}/ubah', [AdminUserController::class, 'edit'])->name('user.edit');
    Route::put('/pengguna/{user}', [AdminUserController::class, 'update'])->name('user.update');
    Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('user.destroy');

    // Kelola arsip (dokumen internal, hanya admin)
    Route::get('/arsip', [AdminArsipController::class, 'index'])->name('arsip.index');
    Route::get('/arsip/tambah', [AdminArsipController::class, 'create'])->name('arsip.create');
    Route::post('/arsip', [AdminArsipController::class, 'store'])->name('arsip.store');
    Route::get('/arsip/{arsip}/ubah', [AdminArsipController::class, 'edit'])->name('arsip.edit');
    Route::get('/arsip/{arsip}/lampiran', [AdminArsipController::class, 'unduh'])->name('arsip.unduh');
    Route::put('/arsip/{arsip}', [AdminArsipController::class, 'update'])->name('arsip.update');
    Route::delete('/arsip/{arsip}', [AdminArsipController::class, 'destroy'])->name('arsip.destroy');

    // Kelola PKH — Sistem Pendukung Keputusan metode SAW
    Route::prefix('pkh')->name('pkh.')->group(function () {
        // Pendaftaran calon (pengajuan warga yang ditinjau admin)
        Route::get('/pendaftaran', [AdminPendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/pendaftaran/{pendaftaran}', [AdminPendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::get('/pendaftaran/{pendaftaran}/foto/{jenis}', [AdminPendaftaranController::class, 'foto'])->name('pendaftaran.foto');
        Route::post('/pendaftaran/{pendaftaran}/verifikasi', [AdminPendaftaranController::class, 'verifikasi'])->name('pendaftaran.verifikasi');
        Route::post('/pendaftaran/{pendaftaran}/tolak', [AdminPendaftaranController::class, 'tolak'])->name('pendaftaran.tolak');
        Route::delete('/pendaftaran/{pendaftaran}', [AdminPendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');

        // Penilaian calon penerima (alternatif = warga terdaftar)
        Route::get('/penilaian', [AdminPkhPenilaianController::class, 'index'])->name('penilaian.index');
        Route::post('/penilaian', [AdminPkhPenilaianController::class, 'store'])->name('penilaian.store');
        Route::get('/penilaian/{alternatif}/nilai', [AdminPkhPenilaianController::class, 'edit'])->name('penilaian.edit');
        Route::put('/penilaian/{alternatif}', [AdminPkhPenilaianController::class, 'update'])->name('penilaian.update');
        Route::delete('/penilaian/{alternatif}', [AdminPkhPenilaianController::class, 'destroy'])->name('penilaian.destroy');

        // Hasil akhir perankingan SAW
        Route::get('/hasil-akhir', [AdminPkhController::class, 'hasil'])->name('hasil');

        // Sub-kriteria (himpunan) tiap kriteria
        Route::post('/{kriteria}/sub', [AdminPkhController::class, 'storeSub'])
            ->whereIn('kriteria', array_keys(AdminPkhController::KRITERIA))->name('sub.store');
        Route::put('/sub/{sub}', [AdminPkhController::class, 'updateSub'])->name('sub.update');
        Route::delete('/sub/{sub}', [AdminPkhController::class, 'destroySub'])->name('sub.destroy');

        // Kelola satu kriteria (paling akhir agar tidak menelan segmen di atas)
        Route::get('/{kriteria}', [AdminPkhController::class, 'show'])
            ->whereIn('kriteria', array_keys(AdminPkhController::KRITERIA))->name('kriteria');
    });

    // Kelola pengaduan
    Route::get('/pengaduan', [AdminPengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/{pengaduan}', [AdminPengaduanController::class, 'show'])->name('pengaduan.show');
    Route::patch('/pengaduan/{pengaduan}/status', [AdminPengaduanController::class, 'updateStatus'])->name('pengaduan.updateStatus');
    Route::delete('/pengaduan/{pengaduan}', [AdminPengaduanController::class, 'destroy'])->name('pengaduan.destroy');
});