@extends('layouts.layout')
@section('title', 'Riwayat Pengaduan - Portal PKH Dinas Sosial Kabupaten Mukomuko')
@section('content')
<style>
    /* Animasi dibuat seminimal mungkin: hanya fade-in halus saat elemen terlihat */
    .reveal {
        opacity: 0;
        transform: translateY(12px);
        transition: opacity .5s ease-out, transform .5s ease-out;
    }
    .reveal.is-visible { opacity: 1; transform: none; }

    @media (prefers-reduced-motion: reduce) {
        .reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
    }
</style>
<noscript><style>.reveal { opacity: 1; transform: none; }</style></noscript>

    <!-- ================= JUDUL HALAMAN ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <nav aria-label="Breadcrumb" class="text-xs text-slate-500">
                <ol class="flex items-center gap-2">
                    <li><a href="{{ url('/') }}" class="hover:text-[#14346B] transition-colors">Beranda</a></li>
                    <li class="text-slate-300">/</li>
                    <li><a href="{{ route('pengaduan') }}" class="hover:text-[#14346B] transition-colors">Pengaduan Masyarakat</a></li>
                    <li class="text-slate-300">/</li>
                    <li class="text-slate-700 font-medium">Riwayat</li>
                </ol>
            </nav>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-block w-7 border border-slate-300 overflow-hidden">
                    <span class="block h-[7px] bg-[#C8102E]"></span>
                    <span class="block h-[7px] bg-white"></span>
                </span>
                <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    Layanan Pengaduan
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-[#0E2650] leading-[1.2] tracking-tight">
                Riwayat Pengaduan
            </h1>
            <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>
            <p class="mt-6 text-base text-slate-600 leading-relaxed max-w-3xl">
                Daftar seluruh pengaduan yang pernah Anda sampaikan beserta status tindak lanjutnya oleh
                petugas Dinas Sosial Kabupaten Mukomuko.
            </p>

            <div class="mt-8 inline-flex border border-slate-200 rounded-md overflow-hidden bg-white text-sm">
                <a href="{{ route('pengaduan') }}" class="px-5 py-2.5 font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
                    Formulir Pengaduan
                </a>
                <a href="{{ route('pengaduan.riwayat') }}" class="px-5 py-2.5 font-semibold bg-[#14346B] text-white border-l border-slate-200">
                    Riwayat Pengaduan
                </a>
            </div>
        </div>
    </section>

    <!-- ================= DAFTAR RIWAYAT ================= -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            @forelse ($pengaduans as $item)
                @php
                    $badge = match ($item->status_pengaduan) {
                        'Baru'         => ['border-[#14346B]/20 bg-[#14346B]/5 text-[#14346B]', 'bg-[#14346B]'],
                        'Pending'      => ['border-amber-200 bg-amber-50 text-amber-700', 'bg-amber-500'],
                        'Dalam Proses' => ['border-blue-200 bg-blue-50 text-blue-700', 'bg-blue-500'],
                        'Selesai'      => ['border-emerald-200 bg-emerald-50 text-emerald-700', 'bg-emerald-500'],
                        'Decline'      => ['border-rose-200 bg-rose-50 text-[#C8102E]', 'bg-[#C8102E]'],
                        default        => ['border-slate-200 bg-slate-50 text-slate-600', 'bg-slate-400'],
                    };
                @endphp
                <article class="reveal bg-white border border-slate-200 rounded-lg mb-4" style="transition-delay: {{ min($loop->index, 5) * 60 }}ms">
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-slate-100">
                        <span class="text-xs font-semibold text-slate-400 tracking-wider">
                            NOMOR LAPORAN #{{ str_pad($item->id_pengaduan, 5, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="inline-flex items-center gap-2 text-[11px] font-semibold px-3 py-1.5 rounded-md border {{ $badge[0] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $badge[1] }}"></span>
                            {{ $item->status_pengaduan }}
                        </span>
                    </div>

                    <div class="px-6 py-5">
                        <h2 class="font-semibold text-slate-900 text-lg">{{ $item->judul_pengaduan }}</h2>
                        <p class="text-sm text-slate-500 leading-relaxed mt-2">
                            {{ \Illuminate\Support\Str::limit($item->isi_pengaduan, 220) }}
                        </p>

                        <dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm border-t border-slate-100 pt-4">
                            <div class="flex items-start gap-3">
                                <dt class="text-slate-400 flex-shrink-0"><i class="fas fa-calendar-day w-4 text-center"></i></dt>
                                <dd class="text-slate-600">{{ $item->tanggal_pengaduan->translatedFormat('d F Y, H:i') }} WIB</dd>
                            </div>
                            <div class="flex items-start gap-3">
                                <dt class="text-slate-400 flex-shrink-0"><i class="fas fa-location-dot w-4 text-center"></i></dt>
                                <dd class="text-slate-600">{{ $item->alamat_pengadu }}</dd>
                            </div>
                        </dl>

                        @if ($item->url_lampiran)
                            <a href="{{ $item->url_lampiran }}" target="_blank" rel="noopener"
                               class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#14346B] hover:gap-3 transition-all">
                                <i class="fas fa-paperclip text-xs"></i> Lihat Bukti Pendukung
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="bg-white border border-slate-200 rounded-lg px-6 py-16 text-center">
                    <span class="w-14 h-14 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-2xl mb-5">
                        <i class="fas fa-inbox"></i>
                    </span>
                    <h2 class="text-lg font-semibold text-slate-900 mb-2">Belum Ada Pengaduan</h2>
                    <p class="text-sm text-slate-500 max-w-md mx-auto leading-relaxed mb-6">
                        Anda belum pernah menyampaikan pengaduan. Silakan gunakan formulir resmi apabila
                        mengalami kendala terkait penyaluran bantuan PKH.
                    </p>
                    <a href="{{ route('pengaduan') }}"
                       class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                        <i class="fas fa-bullhorn text-xs"></i> Ajukan Pengaduan
                    </a>
                </div>
            @endforelse
        </div>
    </section>

    <script>
        (function () {
            // Fade-in halus saat elemen masuk viewport
            var els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) {
                els.forEach(function (el) { el.classList.add('is-visible'); });
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            els.forEach(function (el) { io.observe(el); });
        })();
    </script>
@endsection
