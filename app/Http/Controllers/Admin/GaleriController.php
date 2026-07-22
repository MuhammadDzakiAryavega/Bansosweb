<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\GaleriFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $query = Galeri::query()->withCount('fotos');

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

        $galeris = $query->with('fotos')
            ->orderByDesc('tgl_pelaksanaan')
            ->orderByDesc('id_galeri')
            ->paginate(10)
            ->withQueryString();

        $statistik = [
            'total'     => Galeri::count(),
            'foto'      => GaleriFoto::count(),
            'bulan_ini' => Galeri::whereYear('tgl_pelaksanaan', now()->year)
                ->whereMonth('tgl_pelaksanaan', now()->month)
                ->count(),
            'tahun_ini' => Galeri::whereYear('tgl_pelaksanaan', now()->year)->count(),
        ];

        return view('admin.kelola_galeri.index', [
            'galeris'   => $galeris,
            'statistik' => $statistik,
            'tahunList' => $this->daftarTahun(),
        ]);
    }

    public function create()
    {
        return view('admin.kelola_galeri.create');
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $data['slug'] = Galeri::buatSlug($data['judul_kegiatan']);

        $galeri = Galeri::create($data);
        $this->simpanFoto($request, $galeri);

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', 'Galeri kegiatan berhasil ditambahkan.');
    }

    public function edit(Galeri $galeri)
    {
        return view('admin.kelola_galeri.edit', [
            'galeri' => $galeri->load('fotos'),
        ]);
    }

    public function update(Request $request, Galeri $galeri)
    {
        $data = $this->validasi($request);

        $data['slug'] = Galeri::buatSlug($data['judul_kegiatan'], $galeri->id_galeri);

        $galeri->update($data);

        // Foto yang dicentang untuk dibuang dihapus lebih dulu, baru foto baru ditambahkan.
        $this->hapusFotoTerpilih($request, $galeri);
        $this->simpanFoto($request, $galeri);

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', 'Galeri kegiatan berhasil diperbarui.');
    }

    public function destroy(Galeri $galeri)
    {
        foreach ($galeri->fotos as $foto) {
            $this->hapusBerkas($foto->path);
        }

        $galeri->delete();

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', 'Galeri kegiatan berhasil dihapus.');
    }

    /* ---------- Pembantu ---------- */

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'judul_kegiatan'    => ['required', 'string', 'max:255'],
            'tgl_pelaksanaan'   => ['required', 'date'],
            'deskripsi_singkat' => ['required', 'string', 'max:1000'],
            'foto'              => ['nullable', 'array', 'max:20'],
            'foto.*'            => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_foto'        => ['nullable', 'array'],
            'hapus_foto.*'      => ['integer'],
        ], [
            'required'                => ':attribute wajib diisi.',
            'judul_kegiatan.max'      => 'Judul kegiatan maksimal 255 karakter.',
            'tgl_pelaksanaan.date'    => 'Tanggal pelaksanaan tidak valid.',
            'deskripsi_singkat.max'   => 'Deskripsi singkat maksimal 1000 karakter.',
            'foto.max'                => 'Maksimal 20 foto untuk sekali unggah.',
            'foto.*.image'            => 'Dokumentasi harus berupa berkas gambar.',
            'foto.*.mimes'            => 'Foto dokumentasi harus berformat JPG, PNG, atau WEBP.',
            'foto.*.max'              => 'Ukuran setiap foto maksimal 2 MB.',
        ], [
            'judul_kegiatan'    => 'Judul kegiatan',
            'tgl_pelaksanaan'   => 'Tanggal pelaksanaan',
            'deskripsi_singkat' => 'Deskripsi singkat',
            'foto'              => 'Foto dokumentasi',
        ]);

        // Hanya kolom tabel galeris yang diteruskan ke Eloquent.
        return [
            'judul_kegiatan'    => $data['judul_kegiatan'],
            'tgl_pelaksanaan'   => $data['tgl_pelaksanaan'],
            'deskripsi_singkat' => $data['deskripsi_singkat'],
        ];
    }

    private function simpanFoto(Request $request, Galeri $galeri): void
    {
        if (! $request->hasFile('foto')) {
            return;
        }

        $urutan = (int) $galeri->fotos()->max('urutan');

        foreach ($request->file('foto') as $berkas) {
            $galeri->fotos()->create([
                'path'   => '/storage/' . $berkas->store('galeri', 'public'),
                'urutan' => ++$urutan,
            ]);
        }

        $galeri->unsetRelation('fotos');
    }

    private function hapusFotoTerpilih(Request $request, Galeri $galeri): void
    {
        $idFoto = array_filter((array) $request->input('hapus_foto', []));

        if (! $idFoto) {
            return;
        }

        // Dibatasi pada foto milik galeri ini agar id dari luar tidak bisa dipakai.
        $fotos = $galeri->fotos()->whereIn('id_foto', $idFoto)->get();

        foreach ($fotos as $foto) {
            $this->hapusBerkas($foto->path);
            $foto->delete();
        }

        $galeri->unsetRelation('fotos');
    }

    private function hapusBerkas(?string $url): void
    {
        if (! $url) {
            return;
        }

        Storage::disk('public')->delete(ltrim(str_replace('/storage/', '', $url), '/'));
    }

    /** Daftar tahun pelaksanaan yang tersedia, untuk saringan pada halaman daftar. */
    private function daftarTahun(): array
    {
        return Galeri::query()
            ->selectRaw('DISTINCT ' . $this->ekspresiTahun() . ' as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->map(fn ($tahun) => (int) $tahun)
            ->all();
    }

    /** SQLite dan MySQL memakai fungsi tahun yang berbeda. */
    private function ekspresiTahun(): string
    {
        return Galeri::query()->getConnection()->getDriverName() === 'sqlite'
            ? "strftime('%Y', tgl_pelaksanaan)"
            : 'YEAR(tgl_pelaksanaan)';
    }
}
