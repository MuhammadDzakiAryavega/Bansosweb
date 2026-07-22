@extends('layouts.admin')

@section('title', 'Tulis Berita - Admin Portal PKH')
@section('page-title', 'Tulis Berita')
@section('page-subtitle', 'Susun informasi baru untuk ditampilkan pada halaman Berita portal pengguna.')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.berita.index') }}"
           class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
            <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Daftar Berita
        </a>
    </div>

    @include('admin.kelola_berita.form', [
        'berita'      => null,
        'aksi'        => route('admin.berita.store'),
        'metode'      => 'POST',
        'labelTombol' => 'Simpan Berita',
    ])

@endsection
