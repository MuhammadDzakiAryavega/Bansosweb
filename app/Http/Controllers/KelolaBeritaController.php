<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KelolaBeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::query();

        if ($request->filled('status') && in_array($request->status, Berita::STATUS_LIST, true)) {
            $query->where('status_berita', $request->status);
        }

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

        $beritas = $query->latest('created_at')->paginate(10)->withQueryString();

        $statistik = [
            'total'     => Berita::count(),
            'published' => Berita::where('status_berita', 'Published')->count(),
            'draft'     => Berita::where('status_berita', 'Draft')->count(),
            'kategori'  => Berita::distinct()->count('kategori'),
        ];

        return view('admin.kelola_berita.index', [
            'beritas'      => $beritas,
            'statistik'    => $statistik,
            'statusList'   => Berita::STATUS_LIST,
            'kategoriList' => Berita::KATEGORI_LIST,
        ]);
    }

    public function create()
    {
        return view('admin.kelola_berita.create', [
            'statusList'   => Berita::STATUS_LIST,
            'kategoriList' => Berita::KATEGORI_LIST,
            'penulisList'  => Berita::PENULIS_LIST,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $data['user_id'] = $request->user()->id; // petugas yang mencatat
        $data['slug'] = Berita::buatSlug($data['judul_berita']);
        $data['gambar_cover'] = $this->simpanCover($request);
        $data['tanggal_publikasi'] = $data['status_berita'] === 'Published' ? now() : null;

        Berita::create($data);

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(Berita $berita)
    {
        return view('admin.kelola_berita.edit', [
            'berita'       => $berita,
            'statusList'   => Berita::STATUS_LIST,
            'kategoriList' => Berita::KATEGORI_LIST,
            'penulisList'  => Berita::PENULIS_LIST,
        ]);
    }

    public function update(Request $request, Berita $berita)
    {
        $data = $this->validasi($request);

        $data['slug'] = Berita::buatSlug($data['judul_berita'], $berita->id_berita);

        if ($request->hasFile('gambar_cover')) {
            $this->hapusCover($berita->gambar_cover);
            $data['gambar_cover'] = $this->simpanCover($request);
        }

        // Tanggal terbit dicatat sekali saat berita pertama kali dipublikasikan.
        if ($data['status_berita'] === 'Published') {
            $data['tanggal_publikasi'] = $berita->tanggal_publikasi ?? now();
        } else {
            $data['tanggal_publikasi'] = null;
        }

        $berita->update($data);

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Berita $berita)
    {
        $this->hapusCover($berita->gambar_cover);
        $berita->delete();

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil dihapus.');
    }

    /* ---------- Pembantu ---------- */

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul_berita'  => ['required', 'string', 'max:150'],
            'kategori'      => ['required', 'in:' . implode(',', Berita::KATEGORI_LIST)],
            'penulis'       => ['required', 'in:' . implode(',', Berita::PENULIS_LIST)],
            'status_berita' => ['required', 'in:' . implode(',', Berita::STATUS_LIST)],
            'isi_artikel'   => ['required', 'string'],
            'gambar_cover'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'required'           => ':attribute wajib diisi.',
            'in'                 => 'Pilihan :attribute tidak valid.',
            'judul_berita.max'   => 'Judul berita maksimal 150 karakter.',
            'gambar_cover.image' => 'Gambar sampul harus berupa berkas gambar.',
            'gambar_cover.mimes' => 'Gambar sampul harus berformat JPG, PNG, atau WEBP.',
            'gambar_cover.max'   => 'Ukuran gambar sampul maksimal 2 MB.',
        ], [
            'judul_berita'  => 'Judul berita',
            'kategori'      => 'Kategori',
            'penulis'       => 'Penulis',
            'status_berita' => 'Status',
            'isi_artikel'   => 'Isi artikel',
            'gambar_cover'  => 'Gambar sampul',
        ]);
    }

    private function simpanCover(Request $request): ?string
    {
        if (! $request->hasFile('gambar_cover')) {
            return null;
        }

        return '/storage/' . $request->file('gambar_cover')->store('berita-cover', 'public');
    }

    private function hapusCover(?string $url): void
    {
        if (! $url) {
            return;
        }

        Storage::disk('public')->delete(ltrim(str_replace('/storage/', '', $url), '/'));
    }
}
