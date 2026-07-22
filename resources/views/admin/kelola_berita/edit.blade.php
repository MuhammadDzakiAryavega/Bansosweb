@extends('layouts.admin')

@section('title', 'Ubah Berita - Admin Portal PKH')
@section('page-title', 'Ubah Berita')
@section('page-subtitle', 'Perbarui isi, kategori, atau status publikasi berita.')

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.berita.index') }}"
           class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
            <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Daftar Berita
        </a>

        <div class="flex items-center gap-4 text-xs text-slate-500">
            <span>
                <i class="far fa-clock mr-1.5"></i>
                {{ $berita->status_berita === 'Published' ? 'Terbit' : 'Dibuat' }}
                {{ $berita->tanggalTampil()->translatedFormat('d F Y, H:i') }} WIB
            </span>
            @if ($berita->status_berita === 'Published')
                <a href="{{ route('berita.show', $berita->slug) }}" target="_blank" rel="noopener"
                   class="font-semibold text-[#14346B] hover:text-[#0E2650] transition-colors">
                    <i class="fas fa-arrow-up-right-from-square mr-1.5 text-[10px]"></i> Lihat di portal
                </a>
            @endif
        </div>
    </div>

    @include('admin.kelola_berita.form', [
        'berita'      => $berita,
        'aksi'        => route('admin.berita.update', $berita->id_berita),
        'metode'      => 'PUT',
        'labelTombol' => 'Perbarui Berita',
    ])

@endsection
