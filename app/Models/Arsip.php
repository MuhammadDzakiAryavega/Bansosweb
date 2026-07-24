<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Arsip extends Model
{
    protected $table = 'arsips';
    protected $primaryKey = 'id_arsip';

    /**
     * Klasifikasi bawaan. Admin tetap bisa menambah klasifikasi baru lewat
     * pilihan "Lainnya" pada formulir, jadi daftar ini hanya nilai awal.
     */
    public const KLASIFIKASI_LIST = ['Surat Masuk', 'Surat Keluar', 'Dokumen Penting'];

    public const STATUS_LIST = ['Draft', 'Published'];

    /** Angka romawi bulan untuk penomoran arsip (ARS/001/X/2026). */
    private const BULAN_ROMAWI = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    protected $fillable = [
        'nomor_arsip',
        'tgl_dokumen',
        'judul_arsip',
        'klasifikasi',
        'deskripsi_tambahan',
        'lampiran',
        'lampiran_nama',
        'lampiran_ukuran',
        'status_publikasi',
        'tanggal_publikasi',
    ];

    protected function casts(): array
    {
        return [
            'tgl_dokumen'       => 'date',
            'tanggal_publikasi' => 'datetime',
            'lampiran_ukuran'   => 'integer',
        ];
    }

    /** Hanya arsip yang sudah dipublikasikan. */
    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status_publikasi', 'Published');
    }

    public function sudahTerbit(): bool
    {
        return $this->status_publikasi === 'Published';
    }

    public function labelStatus(): string
    {
        return $this->sudahTerbit() ? 'Terbit' : 'Draf';
    }

    public function adaLampiran(): bool
    {
        return filled($this->lampiran);
    }

    /** Ukuran lampiran dalam satuan yang mudah dibaca, mis. 1,2 MB. */
    public function ukuranLampiran(): string
    {
        $ukuran = (int) $this->lampiran_ukuran;

        return match (true) {
            $ukuran <= 0       => '-',
            $ukuran < 1024     => $ukuran . ' B',
            $ukuran < 1048576  => number_format($ukuran / 1024, 0, ',', '.') . ' KB',
            default            => number_format($ukuran / 1048576, 1, ',', '.') . ' MB',
        };
    }

    /** Ikon Font Awesome sesuai jenis berkas lampiran. */
    public function ikonLampiran(): string
    {
        return match (strtolower(pathinfo((string) $this->lampiran_nama, PATHINFO_EXTENSION))) {
            'pdf'                  => 'fa-file-pdf',
            'doc', 'docx'          => 'fa-file-word',
            'xls', 'xlsx'          => 'fa-file-excel',
            'jpg', 'jpeg', 'png'   => 'fa-file-image',
            default                => 'fa-file-lines',
        };
    }

    public function ringkasan(int $panjang = 120): string
    {
        return Str::limit(strip_tags((string) $this->deskripsi_tambahan), $panjang);
    }

    /** Pilihan klasifikasi pada formulir: bawaan + yang pernah dibuat admin. */
    public static function daftarKlasifikasi(): array
    {
        $tersimpan = static::query()
            ->whereNotNull('klasifikasi')
            ->distinct()
            ->orderBy('klasifikasi')
            ->pluck('klasifikasi')
            ->all();

        return array_values(array_unique(array_merge(self::KLASIFIKASI_LIST, $tersimpan)));
    }

    /**
     * Usulan nomor arsip berikutnya dengan pola ARS/001/X/2026, yaitu
     * urutan tahun berjalan / bulan romawi / tahun. Nomor tetap bisa
     * diubah manual oleh admin pada formulir.
     */
    public static function nomorBerikutnya(?Carbon $tanggal = null): string
    {
        $tanggal ??= now();
        $tahun = (int) $tanggal->year;
        $romawi = self::BULAN_ROMAWI[$tanggal->month - 1];

        $urutan = static::whereYear('tgl_dokumen', $tahun)->count() + 1;

        // Bila nomor usulan sudah terpakai (mis. ada arsip yang dihapus), lanjut ke urutan berikutnya.
        do {
            $nomor = sprintf('ARS/%03d/%s/%d', $urutan, $romawi, $tahun);
            $urutan++;
        } while (static::where('nomor_arsip', $nomor)->exists());

        return $nomor;
    }
}
