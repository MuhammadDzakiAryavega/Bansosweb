<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    public function create()
    {
        return view('Pengguna.Pengaduans.pengaduan');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pengadu'    => ['required', 'string', 'max:255'],
            'email_pengadu'   => ['required', 'email', 'max:255'],
            'no_hp_pengadu'   => ['nullable', 'string', 'max:20'],
            'alamat_pengadu'  => ['required', 'string', 'max:500'],
            'judul_pengaduan' => ['required', 'string', 'max:255'],
            'isi_pengaduan'   => ['required', 'string'],
            'lampiran'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'required'        => ':attribute wajib diisi.',
            'max'             => ':attribute melebihi batas yang diizinkan.',
            'email_pengadu.email' => 'Format alamat surel tidak valid.',
            'lampiran.file'   => 'Bukti pendukung harus berupa berkas.',
            'lampiran.mimes'  => 'Bukti pendukung harus berformat JPG, PNG, atau PDF.',
            'lampiran.max'    => 'Ukuran bukti pendukung maksimal 2 MB.',
        ], [
            'nama_pengadu'    => 'Nama lengkap',
            'email_pengadu'   => 'Alamat surel',
            'no_hp_pengadu'   => 'Nomor telepon',
            'alamat_pengadu'  => 'Alamat lengkap',
            'judul_pengaduan' => 'Judul pengaduan',
            'isi_pengaduan'   => 'Isi pengaduan',
            'lampiran'        => 'Bukti pendukung',
        ]);

        $urlLampiran = null;
        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('lampiran-pengaduan', 'public');
            $urlLampiran = '/storage/' . $path;
        }

        Pengaduan::create([
            'user_id'           => Auth::id(),
            'nama_pengadu'      => $data['nama_pengadu'],
            'email_pengadu'     => $data['email_pengadu'],
            'no_hp_pengadu'     => $data['no_hp_pengadu'] ?? null,
            'alamat_pengadu'    => $data['alamat_pengadu'],
            'judul_pengaduan'   => $data['judul_pengaduan'],
            'isi_pengaduan'     => $data['isi_pengaduan'],
            'tanggal_pengaduan' => now(),
            'status_pengaduan'  => 'Baru',
            'url_lampiran'      => $urlLampiran,
        ]);

        return back()->with('status', 'pengaduan-terkirim');
    }

    public function riwayat()
    {
        $pengaduans = Pengaduan::where('user_id', Auth::id())
            ->latest('tanggal_pengaduan')
            ->get();

        return view('Pengguna.Pengaduans.riwayat', compact('pengaduans'));
    }
}