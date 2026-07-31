<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /** Daftar acuan peran. Ubah di sini bila peran bertambah. */
    public const ROLE_LIST = ['user', 'admin', 'seksi'];

    /** Nama peran dalam bahasa Indonesia untuk ditampilkan di antarmuka. */
    public const ROLE_LABEL = [
        'user'  => 'Masyarakat',
        'admin' => 'Administrator',
        'seksi' => 'Seksi Dinas Sosial',
    ];

    /**
     * Keterangan hak akses tiap peran. Harus selaras dengan pembagian rute
     * pada routes/web.php: admin memegang konten portal & akun, seksi
     * memegang proses PKH, pengaduan, dan arsip.
     */
    public const ROLE_KETERANGAN = [
        'user'  => 'Hanya dapat membaca berita dan mengirim pengaduan.',
        'admin' => 'Mengelola berita, galeri, dan akun pengguna.',
        'seksi' => 'Mengelola PKH, pengaduan, dan arsip.',
    ];

    protected $fillable = [
        'name',
        'nik',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSeksi(): bool
    {
        return $this->role === 'seksi';
    }

    /** Admin maupun seksi sama-sama petugas yang boleh masuk panel. */
    public function isPetugas(): bool
    {
        return $this->isAdmin() || $this->isSeksi();
    }

    /** Pengaduan yang pernah dikirim oleh pengguna ini. */
    public function pengaduans(): HasMany
    {
        return $this->hasMany(Pengaduan::class, 'user_id');
    }

    /** Nama peran akun ini dalam bahasa Indonesia. */
    public function labelRole(): string
    {
        return self::ROLE_LABEL[$this->role] ?? self::ROLE_LABEL['user'];
    }
}