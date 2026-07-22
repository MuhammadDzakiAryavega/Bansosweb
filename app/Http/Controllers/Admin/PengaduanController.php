<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    private const STATUS_LIST = ['Baru', 'Pending', 'Dalam Proses', 'Selesai', 'Decline'];

    public function index(Request $request)
    {
        $query = Pengaduan::query();

        if ($request->filled('status') && in_array($request->status, self::STATUS_LIST, true)) {
            $query->where('status_pengaduan', $request->status);
        }

        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_pengadu', 'like', "%{$keyword}%")
                    ->orWhere('judul_pengaduan', 'like', "%{$keyword}%")
                    ->orWhere('email_pengadu', 'like', "%{$keyword}%");
            });
        }

        $pengaduans = $query->latest('tanggal_pengaduan')->paginate(10)->withQueryString();

        $statistik = [
            'total'         => Pengaduan::count(),
            'baru'          => Pengaduan::where('status_pengaduan', 'Baru')->count(),
            'dalam_proses'  => Pengaduan::where('status_pengaduan', 'Dalam Proses')->count(),
            'selesai'       => Pengaduan::where('status_pengaduan', 'Selesai')->count(),
        ];

        return view('admin.kelola_pengaduan.index', [
            'pengaduans' => $pengaduans,
            'statistik'  => $statistik,
            'statusList' => self::STATUS_LIST,
        ]);
    }

    public function show(Pengaduan $pengaduan)
    {
        return view('admin.kelola_pengaduan.show', [
            'pengaduan'  => $pengaduan,
            'statusList' => self::STATUS_LIST,
        ]);
    }

    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $data = $request->validate([
            'status_pengaduan' => ['required', 'in:' . implode(',', self::STATUS_LIST)],
        ]);

        $pengaduan->update($data);

        return back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    public function destroy(Pengaduan $pengaduan)
    {
        $pengaduan->delete();

        return redirect()
            ->route('admin.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }
}
