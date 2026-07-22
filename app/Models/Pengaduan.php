<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduans';
    protected $primaryKey = 'id_pengaduan';

    protected $fillable = [
        'user_id',
        'nama_pengadu',
        'email_pengadu',
        'no_hp_pengadu',
        'alamat_pengadu',
        'judul_pengaduan',
        'isi_pengaduan',
        'tanggal_pengaduan',
        'status_pengaduan',
        'url_lampiran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengaduan' => 'datetime',
        ];
    }
}