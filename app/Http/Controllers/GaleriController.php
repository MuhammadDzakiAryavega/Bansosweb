<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $query = Galeri::query()->with('fotos')->withCount('fotos');

        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('judul_kegiatan', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi_singkat', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tgl_pelaksanaan', $request->tahun);
        }

        $galeris = $query->orderByDesc('tgl_pelaksanaan')
            ->orderByDesc('id_galeri')
            ->paginate(9)
            ->withQueryString();

        return view('Pengguna.Galeris.index', [
            'galeris'   => $galeris,
            'tahunList' => Galeri::query()
                ->orderByDesc('tgl_pelaksanaan')
                ->pluck('tgl_pelaksanaan')
                ->map(fn ($tanggal) => (int) $tanggal->format('Y'))
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    public function show(Galeri $galeri)
    {
        $lainnya = Galeri::with('fotos')
            ->where('id_galeri', '!=', $galeri->id_galeri)
            ->orderByDesc('tgl_pelaksanaan')
            ->take(3)
            ->get();

        return view('Pengguna.Galeris.show', [
            'galeri'  => $galeri->load('fotos'),
            'lainnya' => $lainnya,
        ]);
    }
}
