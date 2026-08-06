<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alternatif;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PkhPenilaianController extends Controller
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

        // Warga yang belum menjadi calon (lintas desa), untuk pilihan penambahan.
        $sudahJadiCalon = $semua->pluck('user_id')->all();
        $wargaTersedia = User::where('role', 'user')
            ->when($sudahJadiCalon, fn ($q) => $q->whereNotIn('id', $sudahJadiCalon))
            ->orderBy('name')
            ->get();

        return view('admin.kelola_pkh.penilaian', [
            'alternatif'    => $alternatif,
            'wargaTersedia' => $wargaTersedia,
            'kriteria'      => PkhController::KRITERIA,
            'desaList'      => Pendaftaran::DESA,
            'desaAktif'     => $desa,
        ]);
    }

    /** Daftarkan seorang warga sebagai calon penerima. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => [
                'required', 'integer',
                Rule::exists('users', 'id')->where('role', 'user'),
                Rule::unique('pkh_alternatif', 'user_id'),
            ],
            'desa' => ['required', Rule::in(Pendaftaran::DESA)],
        ], [
            'user_id.required' => 'Pilih warga terlebih dahulu.',
            'user_id.exists'   => 'Warga yang dipilih tidak valid.',
            'user_id.unique'   => 'Warga ini sudah terdaftar sebagai calon penerima.',
            'desa.required'    => 'Pilih desa calon terlebih dahulu.',
            'desa.in'          => 'Desa yang dipilih tidak valid.',
        ]);

        Alternatif::create(['user_id' => $data['user_id'], 'desa' => $data['desa']]);

        return redirect()
            ->route('admin.pkh.penilaian.index')
            ->with('success', 'Calon penerima berhasil ditambahkan.');
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
            'kriteria'       => PkhController::KRITERIA,
            'pilihan'        => $alternatif->penilaian->pluck('sub_kriteria_id', 'kriteria')->all(),
            'subPerKriteria' => SubKriteria::orderByDesc('nilai')->orderBy('nama')->get()->groupBy('kriteria'),
            'pendaftaran'    => $pendaftaran,
        ]);
    }

    /** Simpan penilaian: satu sub-kriteria per kriteria (kosong = belum dinilai). */
    public function update(Request $request, Alternatif $alternatif)
    {
        $pilihan = (array) $request->input('sub', []);

        foreach (array_keys(PkhController::KRITERIA) as $slug) {
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
