<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::query()->terbit();

        if ($request->filled('kategori') && in_array($request->kategori, Berita::KATEGORI_LIST, true)) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('judul_berita', 'like', "%{$keyword}%")
                    ->orWhere('isi_artikel', 'like', "%{$keyword}%");
            });
        }

        $beritas = $query->latest('tanggal_publikasi')->paginate(9)->withQueryString();

        return view('Pengguna.Beritas.index', [
            'beritas'      => $beritas,
            'kategoriList' => Berita::KATEGORI_LIST,
            'sorotan'      => Berita::terbit()->latest('tanggal_publikasi')->first(),
        ]);
    }

    public function show(Berita $berita)
    {
        // Berita berstatus draf tidak boleh diakses dari sisi pengguna.
        abort_if($berita->status_berita !== 'Published', 404);

        $terkait = Berita::terbit()
            ->where('kategori', $berita->kategori)
            ->where('id_berita', '!=', $berita->id_berita)
            ->latest('tanggal_publikasi')
            ->take(3)
            ->get();

        return view('Pengguna.Beritas.show', compact('berita', 'terkait'));
    }
}
