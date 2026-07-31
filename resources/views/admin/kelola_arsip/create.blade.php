@extends('layouts.admin')

@section('title', 'Tambah Arsip - Panel Portal PKH')
@section('page-title', 'Tambah Arsip')
@section('page-subtitle', 'Catat surat atau dokumen baru ke dalam daftar arsip Dinas Sosial.')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.arsip.index') }}"
           class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
            <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Daftar Arsip
        </a>
    </div>

    @include('admin.kelola_arsip.form', [
        'arsip'       => null,
        'aksi'        => route('admin.arsip.store'),
        'metode'      => 'POST',
        'labelTombol' => 'Simpan Arsip',
    ])

@endsection
