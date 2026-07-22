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
    public const ROLE_LIST = ['user', 'admin'];

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

    /** Pengaduan yang pernah dikirim oleh pengguna ini. */
    public function pengaduans(): HasMany
    {
        return $this->hasMany(Pengaduan::class, 'user_id');
    }

    /** Nama peran dalam bahasa Indonesia untuk ditampilkan di antarmuka. */
    public function labelRole(): string
    {
        return $this->isAdmin() ? 'Administrator' : 'Masyarakat';
    }
}