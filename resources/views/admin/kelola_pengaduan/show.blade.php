@extends('layouts.admin')

@section('title', 'Detail Pengaduan - Panel Portal PKH')
@section('page-title', 'Detail Pengaduan')
@section('page-subtitle', 'Tinjau isi pengaduan dan perbarui status penanganannya.')

@section('content')

    @php
        $badge = match ($pengaduan->status_pengaduan) {
            'Baru'         => 'bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20',
            'Pending'      => 'bg-amber-50 text-amber-700 border-amber-200',
            'Dalam Proses' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'Selesai'      => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Decline'      => 'bg-rose-50 text-[#C8102E] border-rose-200',
            default        => 'bg-slate-50 text-slate-600 border-slate-200',
        };

        $data_pengadu = [
            ['label' => 'Nama',    'value' => $pengaduan->nama_pengadu],
            ['label' => 'Surel',   'value' => $pengaduan->email_pengadu],
            ['label' => 'No. HP',  'value' => $pengaduan->no_hp_pengadu ?: '-'],
            ['label' => 'Alamat',  'value' => $pengaduan->alamat_pengadu],
        ];
    @endphp

    <div class="mb-6">
        <a href="{{ route('admin.pengaduan.index') }}"
           class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
            <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Daftar Pengaduan
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

        <!-- ================= ISI PENGADUAN ================= -->
        <div class="lg:col-span-8">
            <article class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <header class="px-6 py-6 lg:px-8 lg:py-7 border-b border-slate-200">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
                                Pengaduan No. {{ $pengaduan->id_pengaduan }}
                            </span>
                            <h2 class="text-xl md:text-2xl font-bold text-[#0E2650] mt-2 leading-snug tracking-tight">
                                {{ $pengaduan->judul_pengaduan }}
                            </h2>
                        </div>
                        <span class="flex-shrink-0 text-[11px] font-semibold px-3 py-1.5 rounded-md border {{ $badge }}">
                            {{ $pengaduan->status_pengaduan }}
                        </span>
                    </div>
                    <div class="w-12 h-1 bg-[#C8102E] mt-5"></div>
                    <p class="text-xs text-slate-500 mt-5 flex items-center gap-2">
                        <i class="far fa-calendar text-slate-400"></i>
                        Diajukan {{ $pengaduan->tanggal_pengaduan->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </header>

                <div class="px-6 py-6 lg:px-8 lg:py-7">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Uraian Pengaduan</h3>
                    <p class="text-[15px] text-slate-600 leading-relaxed whitespace-pre-line">{{ $pengaduan->isi_pengaduan }}</p>
                </div>

                @if ($pengaduan->url_lampiran)
                    <footer class="px-6 py-5 lg:px-8 border-t border-slate-200 bg-slate-50">
                        <a href="{{ $pengaduan->url_lampiran }}" target="_blank" rel="noopener"
                           class="group inline-flex items-center gap-2 text-sm font-semibold text-[#14346B] hover:text-[#0E2650] transition-colors">
                            <i class="fas fa-paperclip text-xs"></i> Lihat Lampiran
                            <i class="fas fa-arrow-up-right-from-square text-[10px] opacity-60 transition-transform group-hover:translate-x-0.5"></i>
                        </a>
                    </footer>
                @endif
            </article>
        </div>

        <!-- ================= DATA PENGADU & AKSI ================= -->
        <aside class="lg:col-span-4 space-y-6">

            <!-- Data pengadu -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Data Pengadu</h3>
                </div>
                <dl class="px-5 py-2 text-sm divide-y divide-slate-200">
                    @foreach($data_pengadu as $baris)
                        <div class="py-3">
                            <dt class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">{{ $baris['label'] }}</dt>
                            <dd class="font-semibold text-slate-800 mt-1 break-words">{{ $baris['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <!-- Ubah status -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="bg-[#14346B] px-5 py-4">
                    <h3 class="text-white font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-pen-to-square text-xs opacity-80"></i> Tindak Lanjut
                    </h3>
                    <p class="text-blue-100/80 text-xs mt-1">Perbarui status penanganan pengaduan</p>
                </div>

                <div class="px-5 py-5">
                    <form method="POST" action="{{ route('admin.pengaduan.updateStatus', $pengaduan->id_pengaduan) }}">
                        @csrf
                        @method('PATCH')
                        <label for="status_pengaduan" class="block text-sm font-semibold text-slate-700 mb-2">Status Pengaduan</label>
                        <select name="status_pengaduan" id="status_pengaduan"
                                class="w-full px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                            @foreach ($statusList as $status)
                                <option value="{{ $status }}" @selected($pengaduan->status_pengaduan === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="mt-4 w-full inline-flex items-center justify-center gap-2 bg-[#14346B] text-white py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                            <i class="fas fa-check text-xs"></i> Simpan Status
                        </button>
                    </form>

                    <div class="mt-5 pt-5 border-t border-slate-200">
                        <p class="text-xs text-slate-500 leading-relaxed mb-3">
                            Penghapusan pengaduan bersifat permanen dan tidak dapat dibatalkan.
                        </p>
                        <form method="POST" action="{{ route('admin.pengaduan.destroy', $pengaduan->id_pengaduan) }}"
                              onsubmit="return confirm('Yakin ingin menghapus pengaduan ini? Tindakan ini tidak dapat dibatalkan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 border border-slate-300 text-[#C8102E] py-3 rounded-md text-sm font-semibold hover:bg-[#C8102E] hover:border-[#C8102E] hover:text-white transition-colors">
                                <i class="fas fa-trash text-xs"></i> Hapus Pengaduan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>
    </div>

@endsection
