<?php

use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\PengaduanController as AdminPengaduanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
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

    // Kelola pengaduan
    Route::get('/pengaduan', [AdminPengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/{pengaduan}', [AdminPengaduanController::class, 'show'])->name('pengaduan.show');
    Route::patch('/pengaduan/{pengaduan}/status', [AdminPengaduanController::class, 'updateStatus'])->name('pengaduan.updateStatus');
    Route::delete('/pengaduan/{pengaduan}', [AdminPengaduanController::class, 'destroy'])->name('pengaduan.destroy');
});