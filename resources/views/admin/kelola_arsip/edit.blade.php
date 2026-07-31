@extends('layouts.admin')

@section('title', 'Ubah Arsip - Panel Portal PKH')
@section('page-title', 'Ubah Arsip')
@section('page-subtitle', 'Perbarui identitas dokumen, klasifikasi, lampiran, atau status publikasi arsip.')

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.arsip.index') }}"
           class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
            <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Daftar Arsip
        </a>

        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
            <span class="font-semibold tracking-wider text-slate-600 tabular-nums">{{ $arsip->nomor_arsip }}</span>
            <span class="text-slate-300">|</span>
            <span>
                <i class="far fa-calendar mr-1.5"></i>
                Dokumen {{ $arsip->tgl_dokumen->translatedFormat('d F Y') }}
            </span>
            <span class="text-slate-300">|</span>
            <span>
                <i class="far fa-clock mr-1.5"></i>
                {{ $arsip->sudahTerbit() && $arsip->tanggal_publikasi ? 'Dipublikasikan' : 'Dicatat' }}
                {{ ($arsip->tanggal_publikasi ?? $arsip->created_at)->translatedFormat('d F Y, H:i') }} WIB
            </span>
        </div>
    </div>

    @include('admin.kelola_arsip.form', [
        'arsip'       => $arsip,
        'aksi'        => route('admin.arsip.update', $arsip->id_arsip),
        'metode'      => 'PUT',
        'labelTombol' => 'Perbarui Arsip',
    ])

@endsection
