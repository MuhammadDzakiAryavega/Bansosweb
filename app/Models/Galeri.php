<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Galeri extends Model
{
    protected $table = 'galeris';
    protected $primaryKey = 'id_galeri';

    protected $fillable = [
        'judul_kegiatan',
        'slug',
        'tgl_pelaksanaan',
        'deskripsi_singkat',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pelaksanaan' => 'date',
        ];
    }

    /** Foto dokumentasi kegiatan, urut sesuai urutan unggah. */
    public function fotos(): HasMany
    {
        return $this->hasMany(GaleriFoto::class, 'galeri_id', 'id_galeri')->orderBy('urutan');
    }

    /** Foto pertama, dipakai sebagai sampul pada kartu galeri. */
    public function sampul(): ?GaleriFoto
    {
        return $this->fotos->first();
    }

    /** Ringkasan singkat untuk kartu daftar galeri. */
    public function ringkasan(int $panjang = 150): string
    {
        return Str::limit(strip_tags($this->deskripsi_singkat), $panjang);
    }

    /** Membuat slug unik dari judul; $abaikanId dipakai saat memperbarui galeri. */
    public static function buatSlug(string $judul, ?int $abaikanId = null): string
    {
        $dasar = Str::slug($judul) ?: 'galeri';
        $slug = $dasar;
        $urutan = 2;

        while (
            static::where('slug', $slug)
                ->when($abaikanId, fn ($q) => $q->where('id_galeri', '!=', $abaikanId))
                ->exists()
        ) {
            $slug = $dasar . '-' . $urutan++;
        }

        return $slug;
    }
}
