@extends('layouts.admin')

@section('title', 'Ubah Galeri - Admin Portal PKH')
@section('page-title', 'Ubah Galeri Kegiatan')
@section('page-subtitle', 'Perbarui informasi kegiatan atau kelola foto dokumentasi yang telah diunggah.')

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.galeri.index') }}"
           class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
            <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Daftar Galeri
        </a>

        <div class="flex items-center gap-4 text-xs text-slate-500">
            <span>
                <i class="far fa-calendar mr-1.5"></i>
                Dilaksanakan {{ $galeri->tgl_pelaksanaan->translatedFormat('d F Y') }}
            </span>
            <span>
                <i class="fas fa-camera mr-1.5"></i>
                {{ $galeri->fotos->count() }} foto
            </span>
            <a href="{{ route('galeri.show', $galeri->slug) }}" target="_blank" rel="noopener"
               class="font-semibold text-[#14346B] hover:text-[#0E2650] transition-colors">
                <i class="fas fa-arrow-up-right-from-square mr-1.5 text-[10px]"></i> Lihat di portal
            </a>
        </div>
    </div>

    @include('admin.kelola_galeri.form', [
        'galeri'      => $galeri,
        'aksi'        => route('admin.galeri.update', $galeri->id_galeri),
        'metode'      => 'PUT',
        'labelTombol' => 'Perbarui Galeri',
    ])

@endsection
