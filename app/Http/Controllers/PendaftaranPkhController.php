<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PendaftaranPkhController extends Controller
{
    public function create()
    {
        return view('Pengguna.PendaftaranPkh.daftar', [
            'existing'            => Pendaftaran::where('user_id', Auth::id())->latest()->first(),
            'penghasilanList'     => Pendaftaran::PENGHASILAN,
            'kondisiRumahList'    => Pendaftaran::KONDISI_RUMAH,
            'statusPekerjaanList' => Pendaftaran::STATUS_PEKERJAAN,
            'kepemilikanAsetList' => Pendaftaran::KEPEMILIKAN_ASET,
        ]);
    }

    public function store(Request $request)
    {
        // Cegah pengajuan ganda yang masih berjalan (Baru / sudah Diverifikasi).
        $adaAktif = Pendaftaran::where('user_id', Auth::id())
            ->whereIn('status', ['Baru', 'Diverifikasi'])
            ->exists();

        if ($adaAktif) {
            return redirect()
                ->route('pkh.daftar')
                ->with('error', 'Anda masih memiliki pengajuan PKH yang sedang berjalan.');
        }

        // Aturan, pesan, & label untuk seluruh foto wajib (bukti rumah + foto diri/KTP).
        $fotoRules = $fotoMessages = $fotoAttributes = [];
        foreach (Pendaftaran::jenisFoto() as $field => $label) {
            $namaFoto = str($label)->startsWith('Foto') ? $label : "Foto {$label}";
            $fotoRules[$field] = ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'];
            $fotoMessages["{$field}.required"] = "{$namaFoto} wajib diunggah.";
            $fotoMessages["{$field}.image"]    = "Berkas {$namaFoto} harus berupa gambar.";
            $fotoMessages["{$field}.mimes"]    = "{$namaFoto} harus berformat JPG atau PNG.";
            $fotoMessages["{$field}.max"]      = "Ukuran {$namaFoto} maksimal 5 MB.";
            $fotoAttributes[$field]            = $namaFoto;
        }

        $validated = $request->validate([
            'nama'              => ['required', 'string', 'max:100'],
            'nik'               => ['required', 'digits:16'],
            'desa'              => ['required', Rule::in(Pendaftaran::DESA)],
            'alamat'            => ['required', 'string', 'max:500'],
            'no_hp'             => ['nullable', 'string', 'max:20'],
            'penghasilan'       => ['required', Rule::in(Pendaftaran::PENGHASILAN)],
            'jumlah_tanggungan' => ['required', 'integer', 'min:0', 'max:30'],
            'kondisi_rumah'     => ['required', Rule::in(Pendaftaran::KONDISI_RUMAH)],
            'status_pekerjaan'  => ['required', Rule::in(Pendaftaran::STATUS_PEKERJAAN)],
            'kepemilikan_aset'  => ['required', Rule::in(Pendaftaran::KEPEMILIKAN_ASET)],
        ] + $fotoRules, [
            'required'      => ':attribute wajib diisi.',
            'nik.digits'    => 'NIK harus terdiri dari 16 digit angka.',
            'in'            => 'Pilihan :attribute tidak valid.',
            'jumlah_tanggungan.integer' => 'Jumlah tanggungan harus berupa angka.',
            'jumlah_tanggungan.max'     => 'Jumlah tanggungan tidak wajar.',
        ] + $fotoMessages, [
            'nama'              => 'Nama lengkap',
            'nik'               => 'NIK',
            'desa'              => 'Desa/Kelurahan',
            'alamat'            => 'Alamat lengkap',
            'no_hp'             => 'Nomor telepon',
            'penghasilan'       => 'Penghasilan per bulan',
            'jumlah_tanggungan' => 'Jumlah tanggungan',
            'kondisi_rumah'     => 'Kondisi rumah',
            'status_pekerjaan'  => 'Status pekerjaan',
            'kepemilikan_aset'  => 'Kepemilikan aset',
        ] + $fotoAttributes);

        // Data non-foto; foto disimpan ke disk privat lalu path-nya dilampirkan.
        $data = collect($validated)->except(array_keys(Pendaftaran::jenisFoto()))->all();

        foreach (array_keys(Pendaftaran::jenisFoto()) as $field) {
            $data[$field] = $request->file($field)->store('pkh-pendaftaran', 'local');
        }

        Pendaftaran::create($data + [
            'user_id' => Auth::id(),
            'status'  => 'Baru',
        ]);

        return redirect()
            ->route('pkh.daftar')
            ->with('status', 'pkh-terdaftar');
    }
}
