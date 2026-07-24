<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alternatif;
use App\Models\Pendaftaran;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class PkhController extends Controller
{
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

    /** Halaman kelola satu kriteria PKH beserta sub-kriterianya. */
    public function show(string $kriteria)
    {
        abort_unless(array_key_exists($kriteria, self::KRITERIA), 404);

        return view('admin.kelola_pkh.kriteria', [
            'aktif'        => $kriteria,
            'kriteria'     => self::KRITERIA[$kriteria],
            'daftar'       => self::KRITERIA,
            'subKriteria'  => SubKriteria::where('kriteria', $kriteria)
                ->orderByDesc('nilai')
                ->orderBy('nama')
                ->get(),
        ]);
    }

    public function storeSub(Request $request, string $kriteria)
    {
        abort_unless(array_key_exists($kriteria, self::KRITERIA), 404);

        SubKriteria::create(['kriteria' => $kriteria] + $this->validasiSub($request));

        return redirect()
            ->route('admin.pkh.kriteria', $kriteria)
            ->with('success', 'Sub-kriteria berhasil ditambahkan.');
    }

    public function updateSub(Request $request, SubKriteria $sub)
    {
        $sub->update($this->validasiSub($request));

        return redirect()
            ->route('admin.pkh.kriteria', $sub->kriteria)
            ->with('success', 'Sub-kriteria berhasil diperbarui.');
    }

    public function destroySub(SubKriteria $sub)
    {
        $kriteria = $sub->kriteria;
        $sub->delete();

        return redirect()
            ->route('admin.pkh.kriteria', $kriteria)
            ->with('success', 'Sub-kriteria berhasil dihapus.');
    }

    /** Halaman hasil akhir: perhitungan & perankingan SAW. */
    public function hasil(Request $request)
    {
        $kriteria = self::KRITERIA;

        // Bila satu desa dipilih, normalisasi dihitung dalam desa itu saja
        // (query difilter dulu, sehingga maks/min kolom hanya dari calon desa tsb).
        $desa = $request->filled('desa') && in_array($request->desa, Pendaftaran::DESA, true)
            ? $request->desa
            : null;

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

        // Nilai maksimum & minimum tiap kolom kriteria untuk normalisasi.
        $maks = [];
        $mins = [];
        foreach (array_keys($kriteria) as $slug) {
            $kolom = $lengkap->map(fn ($r) => $r['nilai'][$slug])->all();
            $maks[$slug] = $kolom ? max($kolom) : 0;
            $mins[$slug] = $kolom ? min($kolom) : 0;
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
            ];
        })
            ->sortByDesc('skor')
            ->values();

        return view('admin.kelola_pkh.hasil', [
            'kriteria'  => $kriteria,
            'hasil'     => $hasil,
            'belum'     => $belum->sortBy(fn ($r) => $r['alt']->user->name ?? '')->values(),
            'maks'      => $maks,
            'desaList'  => Pendaftaran::DESA,
            'desaAktif' => $desa,
        ]);
    }

    private function validasiSub(Request $request): array
    {
        return $request->validate([
            'nama'  => ['required', 'string', 'max:120'],
            'nilai' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'nama.required'  => 'Nama sub-kriteria wajib diisi.',
            'nama.max'       => 'Nama sub-kriteria maksimal 120 karakter.',
            'nilai.required' => 'Nilai wajib diisi.',
            'nilai.integer'  => 'Nilai harus berupa angka bulat.',
            'nilai.min'      => 'Nilai minimal 1.',
            'nilai.max'      => 'Nilai maksimal 100.',
        ]);
    }
}
