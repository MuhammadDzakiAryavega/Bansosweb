<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class KelolaPkhPenilaianController extends Controller
{
    /** Daftar calon penerima (alternatif) beserta kelengkapan penilaiannya. */
    public function index(Request $request)
    {
        $semua = Alternatif::with(['user', 'penilaian'])->get();

        $desa = $request->filled('desa') && in_array($request->desa, Pendaftaran::DESA, true)
            ? $request->desa
            : null;

        $alternatif = ($desa ? $semua->where('desa', $desa) : $semua)
            ->sortBy(fn ($a) => $a->user->name ?? '')
            ->values();

        return view('admin.kelola_pkh.penilaian', [
            'alternatif' => $alternatif,
            'kriteria'   => KelolaPkhController::KRITERIA,
            'desaList'   => Pendaftaran::DESA,
            'desaAktif'  => $desa,
        ]);
    }

    /** Formulir penilaian: memilih sub-kriteria tiap kriteria untuk satu calon. */
    public function edit(Alternatif $alternatif)
    {
        $alternatif->load(['user', 'penilaian']);

        // Data pendaftaran warga (bila ada) jadi acuan admin saat memilih sub-kriteria.
        $pendaftaran = $alternatif->user_id
            ? Pendaftaran::where('user_id', $alternatif->user_id)->latest()->first()
            : null;

        return view('admin.kelola_pkh.penilaian_nilai', [
            'alternatif'     => $alternatif,
            'kriteria'       => KelolaPkhController::KRITERIA,
            'pilihan'        => $alternatif->penilaian->pluck('sub_kriteria_id', 'kriteria')->all(),
            'subPerKriteria' => SubKriteria::orderByDesc('nilai')->orderBy('nama')->get()->groupBy('kriteria'),
            'pendaftaran'    => $pendaftaran,
        ]);
    }

    /** Simpan penilaian: satu sub-kriteria per kriteria (kosong = belum dinilai). */
    public function update(Request $request, Alternatif $alternatif)
    {
        $pilihan = (array) $request->input('sub', []);

        foreach (array_keys(KelolaPkhController::KRITERIA) as $slug) {
            $subId = $pilihan[$slug] ?? null;

            if (blank($subId)) {
                Penilaian::where('alternatif_id', $alternatif->id)->where('kriteria', $slug)->delete();
                continue;
            }

            if (! SubKriteria::where('id', $subId)->where('kriteria', $slug)->exists()) {
                return back()->with('error', 'Terdapat pilihan sub-kriteria yang tidak sesuai kriterianya.');
            }

            Penilaian::updateOrCreate(
                ['alternatif_id' => $alternatif->id, 'kriteria' => $slug],
                ['sub_kriteria_id' => $subId],
            );
        }

        return redirect()
            ->route('admin.pkh.penilaian.index')
            ->with('success', 'Penilaian ' . ($alternatif->user->name ?? 'calon') . ' berhasil disimpan.');
    }

    /** Keluarkan seorang warga dari daftar calon (penilaiannya ikut terhapus). */
    public function destroy(Alternatif $alternatif)
    {
        $nama = $alternatif->user->name ?? 'Calon';
        $alternatif->delete();

        return redirect()
            ->route('admin.pkh.penilaian.index')
            ->with('success', $nama . ' dikeluarkan dari daftar calon penerima.');
    }
}
