<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendaftaran extends Model
{
    protected $table = 'pkh_pendaftaran';

    /** Pilihan isian form (self-report warga), selaras kriteria SAW. */
    public const PENGHASILAN = [
        '< Rp1.000.000',
        'Rp1.000.000 – Rp2.000.000',
        'Rp2.000.000 – Rp3.000.000',
        '> Rp3.000.000',
    ];

    public const KONDISI_RUMAH = [
        'Tidak layak huni',
        'Kurang layak huni',
        'Layak huni',
    ];

    public const STATUS_PEKERJAAN = [
        'Tidak bekerja',
        'Buruh / pekerja harian lepas',
        'Wiraswasta / usaha kecil',
        'Karyawan / pegawai tetap',
    ];

    public const KEPEMILIKAN_ASET = [
        'Tidak memiliki aset',
        'Memiliki aset sederhana',
        'Memiliki aset bernilai',
    ];

    public const STATUS_LIST = ['Baru', 'Diverifikasi', 'Ditolak'];

    /** Desa/kelurahan di Kecamatan Teramang Jaya (kategori wilayah). */
    public const DESA = [
        'Pasar Bantal',
        'Teramang Jaya',
        'Nenggalo',
        'Pondok Baru',
        'Bunga Tanjung',
        'Sido Makmur',
        'Lubuk Selandak',
        'Bandar Jaya',
        'Mandi Angin Jaya',
        'Nelan Indah',
        'Pernyah',
        'Batu Ejung',
        'Brangan Mulya',
    ];

    /** Foto bukti kondisi rumah: nama kolom => label tampilan. */
    public const FOTO_RUMAH = [
        'foto_depan'      => 'Tampak Depan Rumah',
        'foto_belakang'   => 'Tampak Belakang Rumah',
        'foto_ruang_tamu' => 'Ruang Tamu',
        'foto_wc'         => 'WC / Kamar Mandi',
    ];

    /** Foto verifikasi identitas: diri sambil memegang KTP. */
    public const FOTO_KTP = [
        'foto_ktp' => 'Foto Diri Memegang KTP',
    ];

    /** Seluruh jenis foto wajib (bukti rumah + verifikasi identitas). */
    public static function jenisFoto(): array
    {
        return self::FOTO_RUMAH + self::FOTO_KTP;
    }

    protected $fillable = [
        'user_id',
        'nama',
        'nik',
        'alamat',
        'desa',
        'no_hp',
        'penghasilan',
        'jumlah_tanggungan',
        'kondisi_rumah',
        'status_pekerjaan',
        'kepemilikan_aset',
        'foto_depan',
        'foto_belakang',
        'foto_ruang_tamu',
        'foto_wc',
        'foto_ktp',
        'status',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_tanggungan' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sudahDitinjau(): bool
    {
        return $this->status !== 'Baru';
    }

    /** Kelas badge Tailwind sesuai status pengajuan. */
    public function badgeStatus(): string
    {
        return match ($this->status) {
            'Diverifikasi' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Ditolak'      => 'bg-rose-50 text-[#C8102E] border-rose-200',
            default        => 'bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20',
        };
    }
}
