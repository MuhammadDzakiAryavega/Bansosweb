@extends('layouts.admin')

@section('title', 'Tambah Pengguna - Admin Portal PKH')
@section('page-title', 'Tambah Pengguna')
@section('page-subtitle', 'Buat akun baru untuk masyarakat atau petugas administrator portal PKH.')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.user.index') }}"
           class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
            <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Daftar Pengguna
        </a>
    </div>

    @include('admin.kelola_user.form', [
        'user'        => null,
        'aksi'        => route('admin.user.store'),
        'metode'      => 'POST',
        'labelTombol' => 'Simpan Pengguna',
    ])

@endsection
