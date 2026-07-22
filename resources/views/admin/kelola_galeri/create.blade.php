@extends('layouts.admin')

@section('title', 'Tambah Galeri - Admin Portal PKH')
@section('page-title', 'Tambah Galeri Kegiatan')
@section('page-subtitle', 'Unggah dokumentasi foto kegiatan untuk ditampilkan pada halaman Galeri portal pengguna.')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.galeri.index') }}"
           class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
            <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Daftar Galeri
        </a>
    </div>

    @include('admin.kelola_galeri.form', [
        'galeri'      => null,
        'aksi'        => route('admin.galeri.store'),
        'metode'      => 'POST',
        'labelTombol' => 'Simpan Galeri',
    ])

@endsection
