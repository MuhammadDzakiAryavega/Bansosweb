<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Pendaftaran;
use App\Models\SubKriteria;
use App\Support\XlsxWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KelolaPkhController extends Controller
{
    /**
     * Ambang batas kelayakan penerima PKH.
     *
     * Calon dengan skor preferensi V di bawah nilai ini dinyatakan tidak
     * menerima bantuan, sekalipun peringkatnya masih tercantum pada hasil akhir.
     */
    public const AMBANG_KELAYAKAN = 0.65;

    /**
     * Kriteria penilaian PKH untuk metode SAW.
     *
     * Bobot & jenis bersifat tetap sesuai ketetapan Dinas Sosial, jadi disimpan
     * sebagai konstanta (satu sumber untuk menu, halaman kriteria, & perhitungan).
     * Sub-kriteria/himpunan tiap kriteria dikelola admin dan tersimpan di basis data.
     *
     * @var array<string, array{kode: string, label: string, icon: string, bobot: float, jenis: string, deskripsi: string}>
     */
    public const KRITERIA = [
        'penghasilan' => [
            'kode'      => 'C1',
            'label'     => 'Penghasilan',
            'icon'      => 'fa-wallet',
            'bobot'     => 0.30,
            'jenis'     => 'benefit',
            'deskripsi' => 'Penghasilan rata-rata rumah tangga per bulan, dipakai untuk menilai kemampuan ekonomi keluarga calon penerima PKH.',
        ],
        'jumlah-tanggungan' => [
            'kode'      => 'C2',
            'label'     => 'Jumlah Tanggungan',
            'icon'      => 'fa-users',
            'bobot'     => 0.25,
            'jenis'     => 'benefit',
            'deskripsi' => 'Banyaknya anggota keluarga yang menjadi tanggungan kepala keluarga, seperti anak sekolah, lansia, atau anggota berkebutuhan khusus.',
        ],
        'kondisi-rumah' => [
            'kode'      => 'C3',
            'label'     => 'Kondisi Rumah',
            'icon'      => 'fa-house',
            'bobot'     => 0.20,
            'jenis'     => 'benefit',
            'deskripsi' => 'Kondisi fisik tempat tinggal, mencakup jenis lantai, dinding, atap, serta kelayakan hunian keluarga.',
        ],
        'status-pekerjaan' => [
            'kode'      => 'C4',
            'label'     => 'Status Pekerjaan',
            'icon'      => 'fa-briefcase',
            'bobot'     => 0.15,
            'jenis'     => 'benefit',
            'deskripsi' => 'Status dan jenis pekerjaan kepala keluarga, misalnya tidak bekerja, buruh harian lepas, atau pekerja tetap.',
        ],
        'kepemilikan-aset' => [
            'kode'      => 'C5',
            'label'     => 'Kepemilikan Aset',
            'icon'      => 'fa-coins',
            'bobot'     => 0.10,
            'jenis'     => 'benefit',
            'deskripsi' => 'Aset berharga yang dimiliki keluarga, seperti kendaraan bermotor, ternak, lahan, atau barang elektronik.',
        ],
    ];

    /**
     * Halaman kelola kriteria: seluruh kriteria C1–C5 beserta sub-kriterianya
     * disatukan dalam satu halaman (satu menu, satu proses bagi petugas seksi).
     */
    public function kriteria()
    {
        return view('admin.kelola_pkh.kriteria', [
            'daftar'      => self::KRITERIA,
            'subKriteria' => SubKriteria::orderByDesc('nilai')
                ->orderBy('nama')
                ->get()
                ->groupBy('kriteria'),
        ]);
    }

    public function storeSub(Request $request, string $kriteria)
    {
        SubKriteria::create(['kriteria' => $kriteria] + $this->validasiSub($request, $kriteria));

        return $this->kembaliKeKriteria($kriteria, 'Sub-kriteria berhasil ditambahkan.');
    }

    public function updateSub(Request $request, SubKriteria $sub)
    {
        $sub->update($this->validasiSub($request, $sub->kriteria));

        return $this->kembaliKeKriteria($sub->kriteria, 'Sub-kriteria berhasil diperbarui.');
    }

    public function destroySub(SubKriteria $sub)
    {
        $kriteria = $sub->kriteria;
        $sub->delete();

        return $this->kembaliKeKriteria($kriteria, 'Sub-kriteria berhasil dihapus.');
    }

    /** Kembali ke halaman kriteria, tepat pada bagian kriteria yang baru diubah. */
    private function kembaliKeKriteria(string $kriteria, string $pesan)
    {
        return redirect()
            ->to(route('admin.pkh.kriteria') . '#' . $kriteria)
            ->with('success', $pesan);
    }

    /** Halaman hasil akhir: perhitungan & perankingan SAW. */
    public function hasil(Request $request)
    {
        $desa = $this->desaTerpilih($request);

        return view('admin.kelola_pkh.hasil', $this->perankingan($desa) + [
            'kriteria'  => self::KRITERIA,
            'ambang'    => self::AMBANG_KELAYAKAN,
            'desaList'  => Pendaftaran::DESA,
            'desaAktif' => $desa,
        ]);
    }

    /**
     * Unduh laporan hasil akhir sebagai berkas Excel (.xlsx).
     *
     * Isi laporan mengikuti penyaring desa yang sedang aktif pada halaman hasil
     * akhir, lengkap dengan keputusan layak/tidak layak terhadap ambang batas.
     */
    public function laporan(Request $request)
    {
        $desa     = $this->desaTerpilih($request);
        $kriteria = self::KRITERIA;
        $ambang   = self::AMBANG_KELAYAKAN;

        ['hasil' => $hasil, 'belum' => $belum, 'maks' => $maks] = $this->perankingan($desa);

        $wilayah   = $desa ? 'Desa ' . $desa : 'Semua Desa (gabungan)';
        $jmlLayak  = $hasil->where('layak', true)->count();
        $kolomAkhir = 4 + count($kriteria) + 3; // identitas + kriteria + skor, status, keterangan

        $x = new XlsxWriter('Hasil Akhir SAW');
        $x->lebarKolom(array_merge([10, 30, 22, 20], array_fill(0, count($kriteria), 11), [12, 16, 38]));

        // ---------- Kepala laporan ----------
        $x->tulis([['LAPORAN HASIL AKHIR SELEKSI PENERIMA PKH', XlsxWriter::JUDUL]])->gabungBarisTerakhir($kolomAkhir);
        $x->tulis([['Metode Simple Additive Weighting (SAW) — Portal PKH Kecamatan Teramang Jaya', XlsxWriter::SUBJUDUL]])
            ->gabungBarisTerakhir($kolomAkhir);
        $x->tulis();

        $x->tulis([['Wilayah', XlsxWriter::LABEL], $wilayah]);
        $x->tulis([['Tanggal cetak', XlsxWriter::LABEL], now()->translatedFormat('d F Y, H:i') . ' WIB']);
        $x->tulis([
            ['Ambang kelayakan', XlsxWriter::LABEL],
            'Skor V < ' . number_format($ambang, 2, ',', '.') . ' dinyatakan TIDAK menerima PKH',
        ]);
        $x->tulis([['Calon dinilai lengkap', XlsxWriter::LABEL], $hasil->count() . ' orang']);
        $x->tulis([
            ['Hasil keputusan', XlsxWriter::LABEL],
            $jmlLayak . ' orang layak menerima, ' . ($hasil->count() - $jmlLayak) . ' orang tidak layak',
        ]);
        $x->tulis();

        // ---------- Tabel perankingan ----------
        $header = [
            ['Peringkat', XlsxWriter::HEADER],
            ['Nama Calon Penerima', XlsxWriter::HEADER],
            ['NIK', XlsxWriter::HEADER],
            ['Desa', XlsxWriter::HEADER],
        ];
        foreach ($kriteria as $k) {
            $header[] = [$k['kode'] . ' — ' . $k['label'], XlsxWriter::HEADER];
        }
        $header[] = ['Skor (V)', XlsxWriter::HEADER];
        $header[] = ['Status', XlsxWriter::HEADER];
        $header[] = ['Keterangan', XlsxWriter::HEADER];

        $x->tulis($header);

        foreach ($hasil as $i => $row) {
            $sel = [
                [$i + 1, XlsxWriter::ANGKA],
                [$row['alt']->user->name ?? '(warga terhapus)', XlsxWriter::TEKS],
                [(string) ($row['alt']->user->nik ?? '—'), XlsxWriter::ANGKA],
                [$row['alt']->desa ?? '—', XlsxWriter::TEKS],
            ];

            foreach (array_keys($kriteria) as $slug) {
                $sel[] = [round($row['norm'][$slug], 3), XlsxWriter::DESIMAL];
            }

            $sel[] = [round($row['skor'], 4), XlsxWriter::SKOR];
            $sel[] = $row['layak']
                ? ['LAYAK', XlsxWriter::LAYAK]
                : ['TIDAK LAYAK', XlsxWriter::TIDAK_LAYAK];
            $sel[] = [
                $row['layak']
                    ? 'Menerima PKH (skor ≥ ' . number_format($ambang, 2, ',', '.') . ')'
                    : 'Tidak menerima PKH (skor < ' . number_format($ambang, 2, ',', '.') . ')',
                XlsxWriter::TEKS,
            ];

            $x->tulis($sel);
        }

        if ($hasil->isEmpty()) {
            $x->tulis([['Belum ada calon yang dinilai lengkap pada cakupan ini.', XlsxWriter::TEKS]])
                ->gabungBarisTerakhir($kolomAkhir);
        }

        // ---------- Matriks keputusan (nilai asli) ----------
        $x->tulis();
        $x->tulis([['MATRIKS KEPUTUSAN (nilai crisp sebelum normalisasi)', XlsxWriter::LABEL]])->gabungBarisTerakhir($kolomAkhir);

        $headerMatriks = [['Nama Calon Penerima', XlsxWriter::HEADER]];
        foreach ($kriteria as $k) {
            $headerMatriks[] = [$k['kode'] . ' — ' . $k['label'], XlsxWriter::HEADER];
        }
        $x->tulis($headerMatriks);

        foreach ($hasil as $row) {
            $sel = [[$row['alt']->user->name ?? '(warga terhapus)', XlsxWriter::TEKS]];
            foreach (array_keys($kriteria) as $slug) {
                $sel[] = [(int) $row['nilai'][$slug], XlsxWriter::ANGKA];
            }
            $x->tulis($sel);
        }

        $selIdeal = [['Nilai ideal (pembagi normalisasi)', XlsxWriter::SOROT]];
        foreach (array_keys($kriteria) as $slug) {
            $selIdeal[] = [$maks[$slug], XlsxWriter::SOROT];
        }
        $x->tulis($selIdeal);

        // ---------- Bobot kriteria ----------
        $x->tulis();
        $x->tulis([['BOBOT KRITERIA', XlsxWriter::LABEL]])->gabungBarisTerakhir($kolomAkhir);
        $x->tulis([
            ['Kode', XlsxWriter::HEADER],
            ['Kriteria', XlsxWriter::HEADER],
            ['Bobot', XlsxWriter::HEADER],
            ['Jenis', XlsxWriter::HEADER],
        ]);
        foreach ($kriteria as $k) {
            $x->tulis([
                [$k['kode'], XlsxWriter::ANGKA],
                [$k['label'], XlsxWriter::TEKS],
                [$k['bobot'], XlsxWriter::DESIMAL],
                [ucfirst($k['jenis']), XlsxWriter::ANGKA],
            ]);
        }

        // ---------- Catatan penilaian yang belum lengkap ----------
        if ($belum->isNotEmpty()) {
            $x->tulis();
            $x->tulis([[
                'Catatan: ' . $belum->count() . ' calon belum dinilai lengkap sehingga tidak diikutkan dalam perankingan — '
                    . $belum->map(fn ($r) => ($r['alt']->user->name ?? 'Tanpa nama') . ' (' . $r['terisi'] . '/' . count($kriteria) . ')')->join(', ') . '.',
                XlsxWriter::LABEL,
            ]])->gabungBarisTerakhir($kolomAkhir);
        }

        $namaBerkas = 'Laporan-Hasil-Akhir-PKH-'
            . Str::slug($desa ?? 'Semua Desa') . '-'
            . now()->format('Ymd-Hi') . '.xlsx';

        $path = $x->simpan(storage_path('app/' . Str::random(16) . '.xlsx'));

        return response()->download($path, $namaBerkas, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    /**
     * Hitung perankingan SAW untuk satu cakupan desa.
     *
     * @return array{hasil: \Illuminate\Support\Collection, belum: \Illuminate\Support\Collection, maks: array<string,int>}
     */
    private function perankingan(?string $desa): array
    {
        $kriteria = self::KRITERIA;

        // Penyaring desa hanya membatasi calon yang diperingkat; skor tiap calon
        // tidak ikut berubah karena pembagi normalisasi berasal dari skala nilai
        // (lihat $maks/$mins di bawah), bukan dari kumpulan calon yang tampil.
        $alternatif = Alternatif::with(['user', 'penilaian.subKriteria'])
            ->when($desa, fn ($q) => $q->where('desa', $desa))
            ->get();

        // Pisahkan alternatif yang penilaiannya sudah lengkap (semua kriteria terisi).
        $lengkap = collect();
        $belum   = collect();

        foreach ($alternatif as $alt) {
            $nilai = $alt->nilaiPerKriteria();

            if (count(array_intersect_key($nilai, $kriteria)) === count($kriteria)) {
                $lengkap->push(['alt' => $alt, 'nilai' => $nilai]);
            } else {
                $belum->push(['alt' => $alt, 'terisi' => count($nilai)]);
            }
        }

        // Pembagi normalisasi diambil dari skala penilaian yang ditetapkan pada
        // Kelola Kriteria — nilai sub-kriteria tertinggi (ideal) dan terendah tiap
        // kriteria — bukan dari nilai calon yang kebetulan terdaftar. Dengan acuan
        // tetap ini skor bersifat absolut: seorang calon tidak otomatis bernilai
        // 1,0000 hanya karena ia satu-satunya pembanding, dan skornya tidak
        // berubah ketika calon lain ditambah, dihapus, atau disaring per desa.
        $skala = SubKriteria::selectRaw('kriteria, MAX(nilai) AS nilai_maks, MIN(nilai) AS nilai_mins')
            ->groupBy('kriteria')
            ->get()
            ->keyBy('kriteria');

        $maks = [];
        $mins = [];
        foreach (array_keys($kriteria) as $slug) {
            $maks[$slug] = (int) ($skala[$slug]->nilai_maks ?? 0);
            $mins[$slug] = (int) ($skala[$slug]->nilai_mins ?? 0);
        }

        // Normalisasi SAW + skor preferensi V = Σ (bobot × nilai ternormalisasi).
        $hasil = $lengkap->map(function ($r) use ($kriteria, $maks, $mins) {
            $norm = [];
            $skor = 0.0;

            foreach ($kriteria as $slug => $k) {
                $x = $r['nilai'][$slug];
                $rij = $k['jenis'] === 'benefit'
                    ? ($maks[$slug] > 0 ? $x / $maks[$slug] : 0)
                    : ($x > 0 ? $mins[$slug] / $x : 0);

                $norm[$slug] = $rij;
                $skor += $k['bobot'] * $rij;
            }

            return [
                'alt'   => $r['alt'],
                'nilai' => $r['nilai'],
                'norm'  => $norm,
                'skor'  => $skor,
                // Keputusan akhir: skor di bawah ambang tidak menerima bantuan.
                // Pembulatan disamakan dengan angka yang ditampilkan agar skor
                // seperti 0,64999 tidak terbaca lolos hanya karena galat pecahan.
                'layak' => round($skor, 4) >= self::AMBANG_KELAYAKAN,
            ];
        })
            ->sortByDesc('skor')
            ->values();

        return [
            'hasil' => $hasil,
            'belum' => $belum->sortBy(fn ($r) => $r['alt']->user->name ?? '')->values(),
            'maks'  => $maks,
        ];
    }

    /** Desa dari penyaring, hanya diterima bila terdaftar pada daftar desa resmi. */
    private function desaTerpilih(Request $request): ?string
    {
        return $request->filled('desa') && in_array($request->desa, Pendaftaran::DESA, true)
            ? $request->desa
            : null;
    }

    /**
     * Karena semua kriteria berbagi satu halaman, galat divalidasi ke kantong
     * pesan miliknya sendiri agar hanya muncul pada kriteria yang bersangkutan.
     */
    private function validasiSub(Request $request, string $kriteria): array
    {
        return Validator::make($request->all(), [
            'nama'  => ['required', 'string', 'max:120'],
            'nilai' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'nama.required'  => 'Nama sub-kriteria wajib diisi.',
            'nama.max'       => 'Nama sub-kriteria maksimal 120 karakter.',
            'nilai.required' => 'Nilai wajib diisi.',
            'nilai.integer'  => 'Nilai harus berupa angka bulat.',
            'nilai.min'      => 'Nilai minimal 1.',
            'nilai.max'      => 'Nilai maksimal 100.',
        ])->validateWithBag(self::bagKriteria($kriteria));
    }

    /** Nama kantong pesan galat untuk satu kriteria. */
    public static function bagKriteria(string $kriteria): string
    {
        return 'sub_' . str_replace('-', '_', $kriteria);
    }
}
