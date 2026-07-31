<?php

namespace Database\Seeders;

use App\Http\Controllers\Admin\PkhController;
use App\Models\Alternatif;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Data contoh untuk memahami alur SPK-SAW PKH.
 *
 * Jalankan: php artisan db:seed --class=PkhDemoSeeder
 * Aman diulang (memakai updateOrCreate). Mengisi: akun seksi demo, sub-kriteria
 * tiap kriteria (skala 1-5), calon penerima + penilaiannya di 3 desa, satu
 * calon belum lengkap, serta contoh pengajuan (Baru & Ditolak).
 */
class PkhDemoSeeder extends Seeder
{
    public function run(): void
    {
        /* 1) Petugas seksi demo (pemegang modul PKH) --------------------- */
        User::updateOrCreate(
            ['email' => 'seksi@pkh.test'],
            // NIK dibedakan dari admin demo (…0001) agar seeder tetap aman diulang
            // pada basis data yang sudah berisi akun admin lama.
            ['name' => 'Seksi Demo', 'nik' => '0000000000000002', 'password' => 'password', 'role' => 'seksi'],
        );

        /* 2) Sub-kriteria (himpunan) tiap kriteria — skala 1-5 ----------- */
        // Makin buruk kondisi ekonomi = nilai makin BESAR (semua kriteria benefit).
        $definisiSub = [
            'penghasilan' => [
                ['< Rp1.000.000', 5], ['Rp1.000.000 – Rp2.000.000', 4],
                ['Rp2.000.000 – Rp3.000.000', 2], ['> Rp3.000.000', 1],
            ],
            'jumlah-tanggungan' => [
                ['Lebih dari 4 orang', 5], ['3 – 4 orang', 4],
                ['1 – 2 orang', 2], ['Tidak ada tanggungan', 1],
            ],
            'kondisi-rumah' => [
                ['Tidak layak huni', 5], ['Kurang layak huni', 3], ['Layak huni', 1],
            ],
            'status-pekerjaan' => [
                ['Tidak bekerja', 5], ['Buruh / pekerja harian lepas', 4],
                ['Wiraswasta / usaha kecil', 2], ['Karyawan / pegawai tetap', 1],
            ],
            'kepemilikan-aset' => [
                ['Tidak memiliki aset', 5], ['Memiliki aset sederhana', 3], ['Memiliki aset bernilai', 1],
            ],
        ];

        // $sub[kriteria][nilai] => id sub-kriteria (untuk memilih saat menilai).
        $sub = [];
        foreach ($definisiSub as $kriteria => $items) {
            foreach ($items as [$nama, $nilai]) {
                $sub[$kriteria][$nilai] = SubKriteria::updateOrCreate(
                    ['kriteria' => $kriteria, 'nama' => $nama],
                    ['nilai' => $nilai],
                )->id;
            }
        }

        $kriteriaSlugs = array_keys(PkhController::KRITERIA); // C1..C5 berurutan

        /* 3) Calon penerima + penilaian lengkap -------------------------- */
        // [nama, nik, desa, penghasilan, tanggungan, rumah, pekerjaan, aset]
        $calon = [
            ['Sarni',          '1771010101010001', 'Pasar Bantal', 5, 5, 5, 5, 5],
            ['Budi Hartono',   '1771010101010002', 'Pasar Bantal', 2, 2, 3, 2, 3],
            ['Siti Aminah',    '1771010101010003', 'Pasar Bantal', 4, 4, 5, 4, 3],
            ['Joko Susilo',    '1771010101010004', 'Nenggalo',     5, 4, 3, 4, 5],
            ['Wati Lestari',   '1771010101010005', 'Nenggalo',     2, 2, 1, 1, 1],
            ['Rahmat Hidayat', '1771010101010006', 'Nenggalo',     4, 5, 5, 5, 3],
            ['Yanti Marlina',  '1771010101010007', 'Bandar Jaya',  4, 4, 3, 4, 3],
        ];

        foreach ($calon as [$nama, $nik, $desa, $n1, $n2, $n3, $n4, $n5]) {
            $user = User::updateOrCreate(
                ['nik' => $nik],
                ['name' => $nama, 'email' => str($nama)->slug() . '@warga.test', 'password' => 'password', 'role' => 'user'],
            );
            $alt = Alternatif::updateOrCreate(['user_id' => $user->id], ['desa' => $desa]);

            foreach ([$n1, $n2, $n3, $n4, $n5] as $i => $nilai) {
                Penilaian::updateOrCreate(
                    ['alternatif_id' => $alt->id, 'kriteria' => $kriteriaSlugs[$i]],
                    ['sub_kriteria_id' => $sub[$kriteriaSlugs[$i]][$nilai]],
                );
            }
        }

        /* 4) Satu calon BELUM lengkap (hanya 3 dari 5 kriteria) ---------- */
        $dedi = User::updateOrCreate(
            ['nik' => '1771010101010008'],
            ['name' => 'Dedi Kurniawan', 'email' => 'dedi-kurniawan@warga.test', 'password' => 'password', 'role' => 'user'],
        );
        $altDedi = Alternatif::updateOrCreate(['user_id' => $dedi->id], ['desa' => 'Bandar Jaya']);
        foreach (['penghasilan' => 4, 'jumlah-tanggungan' => 4, 'kondisi-rumah' => 3] as $slug => $nilai) {
            Penilaian::updateOrCreate(
                ['alternatif_id' => $altDedi->id, 'kriteria' => $slug],
                ['sub_kriteria_id' => $sub[$slug][$nilai]],
            );
        }

        /* 5) Contoh pengajuan (Pendaftaran Masuk) ------------------------ */
        // (a) Baru — siap diverifikasi seksi (akun ada, belum jadi calon).
        $marni = User::updateOrCreate(
            ['nik' => '1771010101010010'],
            ['name' => 'Marni', 'email' => 'marni@warga.test', 'password' => 'password', 'role' => 'user'],
        );
        Pendaftaran::updateOrCreate(
            ['nik' => '1771010101010010'],
            [
                'user_id' => $marni->id, 'nama' => 'Marni', 'desa' => 'Pernyah',
                'alamat' => 'Dusun I, RT 002 / RW 001', 'no_hp' => '081200000010',
                'penghasilan' => Pendaftaran::PENGHASILAN[0], 'jumlah_tanggungan' => 4,
                'kondisi_rumah' => Pendaftaran::KONDISI_RUMAH[0],
                'status_pekerjaan' => Pendaftaran::STATUS_PEKERJAAN[0],
                'kepemilikan_aset' => Pendaftaran::KEPEMILIKAN_ASET[0],
                'status' => 'Baru',
            ],
        );

        // (b) Ditolak — dengan catatan petugas.
        $sukini = User::updateOrCreate(
            ['nik' => '1771010101010011'],
            ['name' => 'Sukini', 'email' => 'sukini@warga.test', 'password' => 'password', 'role' => 'user'],
        );
        Pendaftaran::updateOrCreate(
            ['nik' => '1771010101010011'],
            [
                'user_id' => $sukini->id, 'nama' => 'Sukini', 'desa' => 'Teramang Jaya',
                'alamat' => 'Dusun III, RT 001 / RW 002', 'no_hp' => '081200000011',
                'penghasilan' => Pendaftaran::PENGHASILAN[3], 'jumlah_tanggungan' => 1,
                'kondisi_rumah' => Pendaftaran::KONDISI_RUMAH[2],
                'status_pekerjaan' => Pendaftaran::STATUS_PEKERJAAN[3],
                'kepemilikan_aset' => Pendaftaran::KEPEMILIKAN_ASET[2],
                'status' => 'Ditolak', 'catatan_admin' => 'Kondisi ekonomi dinilai belum memenuhi kriteria penerima.',
            ],
        );

        $this->command?->info('Seeder PKH selesai. Login seksi: seksi@pkh.test / password');
    }
}
