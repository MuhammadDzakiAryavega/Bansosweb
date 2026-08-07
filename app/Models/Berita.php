<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $table = 'beritas';
    protected $primaryKey = 'id_berita';

    /**
     * Daftar acuan. Ubah di sini bila kategori/penulis/status bertambah,
     * dan sesuaikan pula enum pada migrasi tabel beritas.
     */
    public const KATEGORI_LIST = [
        'Pengumuman',
        'Kegiatan',
        'Penyaluran Bantuan',
        'Sosialisasi',
        'Informasi Program',
    ];

    public const PENULIS_LIST = ['Admin Utama', 'Editor'];

    public const STATUS_LIST = ['Draft', 'Published'];

    protected $fillable = [
        'user_id',
        'judul_berita',
        'slug',
        'kategori',
        'penulis',
        'status_berita',
        'gambar_cover',
        'isi_artikel',
        'tanggal_publikasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_publikasi' => 'datetime',
        ];
    }

    /** Petugas yang mencatat berita ini. */
    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Hanya berita yang sudah terbit (dipakai sisi pengguna). */
    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status_berita', 'Published');
    }

    /** Ringkasan singkat untuk kartu daftar berita. */
    public function ringkasan(int $panjang = 150): string
    {
        return Str::limit(strip_tags($this->isi_artikel), $panjang);
    }

    /** Tanggal yang ditampilkan: tanggal terbit, atau tanggal dibuat bila masih draf. */
    public function tanggalTampil(): \Illuminate\Support\Carbon
    {
        return $this->tanggal_publikasi ?? $this->created_at;
    }

    /** Membuat slug unik dari judul; $abaikanId dipakai saat memperbarui berita. */
    public static function buatSlug(string $judul, ?int $abaikanId = null): string
    {
        $dasar = Str::slug($judul) ?: 'berita';
        $slug = $dasar;
        $urutan = 2;

        while (
            static::where('slug', $slug)
                ->when($abaikanId, fn ($q) => $q->where('id_berita', '!=', $abaikanId))
                ->exists()
        ) {
            $slug = $dasar . '-' . $urutan++;
        }

        return $slug;
    }
}
