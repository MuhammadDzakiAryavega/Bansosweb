<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KelolaArsipController extends Controller
{
    /** Nilai penanda bahwa admin memilih membuat klasifikasi baru. */
    private const KLASIFIKASI_BARU = '__baru__';

    /** Lampiran disimpan di disk privat agar hanya bisa diunduh lewat rute admin. */
    private const DISK = 'local';

    /** Selaras dengan panjang kolom arsips.lampiran_nama pada migrasi. */
    private const PANJANG_NAMA_LAMPIRAN = 150;

    public function index(Request $request)
    {
        $klasifikasiList = Arsip::daftarKlasifikasi();
        $query = Arsip::query();

        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_arsip', 'like', "%{$keyword}%")
                    ->orWhere('judul_arsip', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi_tambahan', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('klasifikasi') && in_array($request->klasifikasi, $klasifikasiList, true)) {
            $query->where('klasifikasi', $request->klasifikasi);
        }

        if ($request->filled('status') && in_array($request->status, Arsip::STATUS_LIST, true)) {
            $query->where('status_publikasi', $request->status);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tgl_dokumen', $request->tahun);
        }

        $arsips = $query->orderByDesc('tgl_dokumen')
            ->orderByDesc('id_arsip')
            ->paginate(10)
            ->withQueryString();

        $statistik = [
            'total'    => Arsip::count(),
            'terbit'   => Arsip::where('status_publikasi', 'Published')->count(),
            'draft'    => Arsip::where('status_publikasi', 'Draft')->count(),
            'lampiran' => Arsip::whereNotNull('lampiran')->count(),
        ];

        return view('admin.kelola_arsip.index', [
            'arsips'          => $arsips,
            'statistik'       => $statistik,
            'klasifikasiList' => $klasifikasiList,
            'statusList'      => Arsip::STATUS_LIST,
            'tahunList'       => $this->daftarTahun(),
        ]);
    }

    public function create()
    {
        return view('admin.kelola_arsip.create', [
            'klasifikasiList' => Arsip::daftarKlasifikasi(),
            'statusList'      => Arsip::STATUS_LIST,
            'nomorUsulan'     => Arsip::nomorBerikutnya(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $data = array_merge($data, $this->simpanLampiran($request));
        $data['user_id'] = $request->user()->id; // petugas yang mengunggah
        $data['tanggal_publikasi'] = $data['status_publikasi'] === 'Published' ? now() : null;

        Arsip::create($data);

        return redirect()
            ->route('admin.arsip.index')
            ->with('success', 'Arsip berhasil ditambahkan.');
    }

    public function edit(Arsip $arsip)
    {
        return view('admin.kelola_arsip.edit', [
            'arsip'           => $arsip,
            'klasifikasiList' => Arsip::daftarKlasifikasi(),
            'statusList'      => Arsip::STATUS_LIST,
        ]);
    }

    public function update(Request $request, Arsip $arsip)
    {
        $data = $this->validasi($request, $arsip);

        // Lampiran lama dibuang bila dicentang untuk dihapus atau digantikan berkas baru.
        if ($request->boolean('hapus_lampiran') || $request->hasFile('lampiran')) {
            $this->hapusBerkas($arsip->lampiran);
            $data = array_merge($data, [
                'lampiran'        => null,
                'lampiran_nama'   => null,
                'lampiran_ukuran' => null,
            ]);
        }

        $data = array_merge($data, $this->simpanLampiran($request));

        // Tanggal publikasi dicatat sekali saat arsip pertama kali dipublikasikan.
        $data['tanggal_publikasi'] = $data['status_publikasi'] === 'Published'
            ? ($arsip->tanggal_publikasi ?? now())
            : null;

        $arsip->update($data);

        return redirect()
            ->route('admin.arsip.index')
            ->with('success', 'Arsip berhasil diperbarui.');
    }

    public function destroy(Arsip $arsip)
    {
        $this->hapusBerkas($arsip->lampiran);
        $arsip->delete();

        return redirect()
            ->route('admin.arsip.index')
            ->with('success', 'Arsip berhasil dihapus.');
    }

    /** Unduh lampiran. Berada di dalam grup admin, jadi berkas tidak terbuka untuk umum. */
    public function unduh(Arsip $arsip): StreamedResponse
    {
        abort_unless($arsip->adaLampiran() && Storage::disk(self::DISK)->exists($arsip->lampiran), 404);

        return Storage::disk(self::DISK)->download($arsip->lampiran, $arsip->lampiran_nama);
    }

    /* ---------- Pembantu ---------- */

    private function validasi(Request $request, ?Arsip $arsip = null): array
    {
        $klasifikasiList = Arsip::daftarKlasifikasi();

        $data = $request->validate([
            'nomor_arsip' => [
                'required', 'string', 'max:60', 'regex:/^[A-Za-z0-9\/\.\-\s]+$/',
                Rule::unique('arsips', 'nomor_arsip')->ignore($arsip?->id_arsip, 'id_arsip'),
            ],
            'tgl_dokumen'        => ['required', 'date'],
            'judul_arsip'        => ['required', 'string', 'max:150'],
            'klasifikasi'        => ['required', Rule::in(array_merge($klasifikasiList, [self::KLASIFIKASI_BARU]))],
            'klasifikasi_baru'   => ['nullable', 'required_if:klasifikasi,' . self::KLASIFIKASI_BARU, 'string', 'max:60'],
            'deskripsi_tambahan' => ['nullable', 'string', 'max:2000'],
            'lampiran'           => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:5120'],
            'status_publikasi'   => ['required', Rule::in(Arsip::STATUS_LIST)],
        ], [
            'required'              => ':attribute wajib diisi.',
            'in'                    => 'Pilihan :attribute tidak valid.',
            'nomor_arsip.max'       => 'Nomor arsip maksimal 60 karakter.',
            'nomor_arsip.regex'     => 'Nomor arsip hanya boleh berisi huruf, angka, garis miring, titik, dan tanda hubung.',
            'nomor_arsip.unique'    => 'Nomor arsip ini sudah dipakai oleh arsip lain.',
            'tgl_dokumen.date'      => 'Tanggal dokumen tidak valid.',
            'judul_arsip.max'       => 'Judul arsip maksimal 150 karakter.',
            'klasifikasi_baru.required_if' => 'Nama klasifikasi baru wajib diisi.',
            'klasifikasi_baru.max'  => 'Nama klasifikasi baru maksimal 60 karakter.',
            'deskripsi_tambahan.max' => 'Deskripsi tambahan maksimal 2000 karakter.',
            'lampiran.file'         => 'Lampiran harus berupa berkas.',
            'lampiran.mimes'        => 'Lampiran harus berformat PDF, DOC, DOCX, XLS, XLSX, JPG, atau PNG.',
            'lampiran.max'          => 'Ukuran lampiran maksimal 5 MB.',
        ], [
            'nomor_arsip'        => 'Nomor arsip',
            'tgl_dokumen'        => 'Tanggal dokumen',
            'judul_arsip'        => 'Judul arsip',
            'klasifikasi'        => 'Klasifikasi',
            'klasifikasi_baru'   => 'Klasifikasi baru',
            'deskripsi_tambahan' => 'Deskripsi tambahan',
            'lampiran'           => 'Lampiran',
            'status_publikasi'   => 'Status publikasi',
        ]);

        // Hanya kolom tabel arsips yang diteruskan ke Eloquent.
        return [
            'nomor_arsip'        => trim($data['nomor_arsip']),
            'tgl_dokumen'        => $data['tgl_dokumen'],
            'judul_arsip'        => $data['judul_arsip'],
            'klasifikasi'        => $data['klasifikasi'] === self::KLASIFIKASI_BARU
                ? trim($data['klasifikasi_baru'])
                : $data['klasifikasi'],
            'deskripsi_tambahan' => $data['deskripsi_tambahan'] ?? null,
            'status_publikasi'   => $data['status_publikasi'],
        ];
    }

    /** @return array<string, mixed> kolom lampiran, kosong bila tidak ada berkas baru. */
    private function simpanLampiran(Request $request): array
    {
        if (! $request->hasFile('lampiran')) {
            return [];
        }

        $berkas = $request->file('lampiran');

        return [
            'lampiran'        => $berkas->store('arsip', self::DISK),
            'lampiran_nama'   => static::pangkasNamaBerkas($berkas->getClientOriginalName()),
            'lampiran_ukuran' => $berkas->getSize(),
        ];
    }

    /**
     * Memangkas nama berkas agar muat pada kolom lampiran_nama (varchar 150),
     * dengan tetap mempertahankan ekstensinya supaya ikon & unduhan tetap benar.
     */
    private static function pangkasNamaBerkas(string $nama): string
    {
        if (mb_strlen($nama) <= self::PANJANG_NAMA_LAMPIRAN) {
            return $nama;
        }

        $ekstensi = pathinfo($nama, PATHINFO_EXTENSION);
        $akhiran = $ekstensi === '' ? '' : '.' . $ekstensi;

        return mb_substr($nama, 0, self::PANJANG_NAMA_LAMPIRAN - mb_strlen($akhiran)) . $akhiran;
    }

    private function hapusBerkas(?string $path): void
    {
        if ($path) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    /** Daftar tahun dokumen yang tersedia, untuk saringan pada halaman daftar. */
    private function daftarTahun(): array
    {
        return Arsip::query()
            ->selectRaw('DISTINCT ' . $this->ekspresiTahun() . ' as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->map(fn ($tahun) => (int) $tahun)
            ->all();
    }

    /** SQLite dan MySQL memakai fungsi tahun yang berbeda. */
    private function ekspresiTahun(): string
    {
        return Arsip::query()->getConnection()->getDriverName() === 'sqlite'
            ? "strftime('%Y', tgl_dokumen)"
            : 'YEAR(tgl_dokumen)';
    }
}
