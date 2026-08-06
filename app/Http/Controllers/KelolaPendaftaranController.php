<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alternatif;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with('user');

        if ($request->filled('status') && in_array($request->status, Pendaftaran::STATUS_LIST, true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('desa') && in_array($request->desa, Pendaftaran::DESA, true)) {
            $query->where('desa', $request->desa);
        }

        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%");
            });
        }

        return view('admin.kelola_pkh.pendaftaran_index', [
            'pendaftaran' => $query->latest()->paginate(10)->withQueryString(),
            'statusList'  => Pendaftaran::STATUS_LIST,
            'desaList'    => Pendaftaran::DESA,
            'statistik'   => [
                'total'        => Pendaftaran::count(),
                'baru'         => Pendaftaran::where('status', 'Baru')->count(),
                'diverifikasi' => Pendaftaran::where('status', 'Diverifikasi')->count(),
                'ditolak'      => Pendaftaran::where('status', 'Ditolak')->count(),
            ],
        ]);
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load('user');

        return view('admin.kelola_pkh.pendaftaran_show', [
            'pendaftaran' => $pendaftaran,
            'sudahCalon'  => $pendaftaran->user_id
                && Alternatif::where('user_id', $pendaftaran->user_id)->exists(),
        ]);
    }

    /** Setujui pengajuan: warga ditambahkan sebagai calon penerima (alternatif SAW). */
    public function verifikasi(Pendaftaran $pendaftaran)
    {
        if (! $pendaftaran->user_id) {
            return back()->with('error', 'Akun pengaju tidak ditemukan sehingga tidak dapat dijadikan calon.');
        }

        Alternatif::updateOrCreate(
            ['user_id' => $pendaftaran->user_id],
            ['desa' => $pendaftaran->desa],
        );
        $pendaftaran->update(['status' => 'Diverifikasi', 'catatan_admin' => null]);

        return redirect()
            ->route('admin.pkh.pendaftaran.show', $pendaftaran)
            ->with('success', $pendaftaran->nama . ' diverifikasi dan ditambahkan sebagai calon penerima. Lanjutkan ke Penilaian Calon.');
    }

    public function tolak(Request $request, Pendaftaran $pendaftaran)
    {
        $data = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ], [
            'catatan_admin.max' => 'Catatan maksimal 500 karakter.',
        ]);

        $pendaftaran->update([
            'status'        => 'Ditolak',
            'catatan_admin' => $data['catatan_admin'] ?? null,
        ]);

        return redirect()
            ->route('admin.pkh.pendaftaran.show', $pendaftaran)
            ->with('success', 'Pengajuan ' . $pendaftaran->nama . ' ditolak.');
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        foreach (array_keys(Pendaftaran::jenisFoto()) as $field) {
            if ($pendaftaran->{$field}) {
                Storage::disk('local')->delete($pendaftaran->{$field});
            }
        }

        $pendaftaran->delete();

        return redirect()
            ->route('admin.pkh.pendaftaran.index')
            ->with('success', 'Pengajuan berhasil dihapus.');
    }

    /** Tampilkan satu foto bukti kondisi rumah (disk privat, hanya admin). */
    public function foto(Pendaftaran $pendaftaran, string $jenis): BinaryFileResponse
    {
        abort_unless(array_key_exists($jenis, Pendaftaran::jenisFoto()), 404);

        $path = $pendaftaran->{$jenis};
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }
}
