@extends('layouts.layout')
@section('title', 'Galeri Kegiatan - Portal PKH Dinas Sosial Kabupaten Mukomuko')
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

@php
    $adaFilter = request()->filled('cari') || request()->filled('tahun');
@endphp

    <!-- ================= JUDUL HALAMAN ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-14">
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-block w-7 border border-slate-300 overflow-hidden">
                    <span class="block h-[7px] bg-[#C8102E]"></span>
                    <span class="block h-[7px] bg-white"></span>
                </span>
                <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    Dokumentasi Publik
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-end">
                <div class="lg:col-span-7">
                    <h1 class="text-3xl md:text-4xl font-bold text-[#0E2650] leading-[1.2] tracking-tight">
                        Galeri Kegiatan
                    </h1>
                    <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>
                    <p class="mt-6 text-base text-slate-600 leading-relaxed max-w-2xl">
                        Dokumentasi foto pelaksanaan kegiatan Program Keluarga Harapan Kecamatan Teramang Jaya,
                        mulai dari penyaluran bantuan hingga sosialisasi program oleh Dinas Sosial Kabupaten Mukomuko.
                    </p>
                </div>

                <div class="lg:col-span-5">
                    <form method="GET" action="{{ route('galeri.index') }}" class="flex gap-2">
                        @if (request('tahun'))
                            <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                        @endif
                        <label for="cari" class="sr-only">Cari kegiatan</label>
                        <div class="relative flex-1">
                            <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" id="cari" name="cari" value="{{ request('cari') }}"
                                   placeholder="Cari kegiatan..."
                                   class="w-full pl-10 pr-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                        </div>
                        <button type="submit"
                                class="bg-[#14346B] text-white px-5 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors flex-shrink-0">
                            Cari
                        </button>
                    </form>
                </div>
            </div>

            <!-- Saringan tahun pelaksanaan -->
            @if (count($tahunList) > 0)
                <div class="mt-8 flex flex-wrap gap-2">
                    <a href="{{ route('galeri.index', request('cari') ? ['cari' => request('cari')] : []) }}"
                       class="px-4 py-2 rounded-md text-sm font-semibold border transition-colors {{ ! request('tahun') ? 'bg-[#14346B] text-white border-[#14346B]' : 'border-slate-300 text-slate-600 hover:border-[#14346B] hover:text-[#14346B]' }}">
                        Semua Tahun
                    </a>
                    @foreach ($tahunList as $tahun)
                        <a href="{{ route('galeri.index', array_filter(['tahun' => $tahun, 'cari' => request('cari')])) }}"
                           class="px-4 py-2 rounded-md text-sm font-semibold border transition-colors {{ (string) request('tahun') === (string) $tahun ? 'bg-[#14346B] text-white border-[#14346B]' : 'border-slate-300 text-slate-600 hover:border-[#14346B] hover:text-[#14346B]' }}">
                            {{ $tahun }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- ================= DAFTAR GALERI ================= -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">

            @if ($adaFilter)
                <p class="text-sm text-slate-600 mb-6">
                    Menampilkan <span class="font-semibold text-slate-800">{{ $galeris->total() }}</span> kegiatan
                    @if (request('tahun')) pada tahun <span class="font-semibold text-slate-800">{{ request('tahun') }}</span> @endif
                    @if (request('cari')) untuk kata kunci <span class="font-semibold text-slate-800">"{{ request('cari') }}"</span> @endif
                    &middot;
                    <a href="{{ route('galeri.index') }}" class="font-semibold text-[#14346B] hover:underline">Atur ulang</a>
                </p>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($galeris as $item)
                    @php $sampul = $item->sampul(); @endphp
                    <a href="{{ route('galeri.show', $item->slug) }}"
                       class="reveal group bg-white border border-slate-200 rounded-lg overflow-hidden hover:border-[#14346B] transition-colors flex flex-col"
                       style="transition-delay: {{ $loop->index * 60 }}ms">
                        <div class="relative aspect-[16/9] bg-slate-100 overflow-hidden">
                            @if ($sampul)
                                <img src="{{ $sampul->path }}" alt="{{ $item->judul_kegiatan }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-2xl">
                                    <i class="fas fa-images"></i>
                                </div>
                            @endif
                            @if ($item->fotos_count > 0)
                                <span class="absolute bottom-3 right-3 bg-[#0E2650]/85 text-white text-[11px] font-semibold px-2.5 py-1 rounded-md">
                                    <i class="fas fa-camera mr-1.5 text-[10px]"></i>{{ $item->fotos_count }} foto
                                </span>
                            @endif
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20 self-start">
                                {{ $item->tgl_pelaksanaan->translatedFormat('F Y') }}
                            </span>
                            <h2 class="font-semibold text-slate-900 mt-4 leading-snug">{{ $item->judul_kegiatan }}</h2>
                            <p class="text-sm text-slate-500 leading-relaxed mt-3 flex-grow">{{ $item->ringkasan(110) }}</p>
                            <p class="text-xs text-slate-500 mt-4 pt-4 border-t border-slate-100">
                                <i class="far fa-calendar mr-1.5"></i>
                                {{ $item->tgl_pelaksanaan->translatedFormat('d M Y') }}
                            </p>
                            <span class="mt-4 text-sm font-semibold text-[#14346B] inline-flex items-center gap-2">
                                Lihat dokumentasi
                                <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 bg-white border border-slate-200 rounded-lg px-6 py-16 text-center">
                        <span class="w-14 h-14 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-2xl mb-4">
                            <i class="fas fa-images"></i>
                        </span>
                        <p class="font-semibold text-slate-800">Belum ada dokumentasi kegiatan</p>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ $adaFilter
                                ? 'Coba ubah kata kunci atau pilih tahun lain.'
                                : 'Dokumentasi kegiatan dari Dinas Sosial akan tampil di halaman ini.' }}
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($galeris->hasPages())
                <div class="mt-10">
                    {{ $galeris->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- ================= AJAKAN PENGADUAN ================= -->
    <section class="bg-[#0E2650]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-white">Ada informasi yang perlu diklarifikasi?</h2>
                    <p class="text-blue-100/70 text-sm mt-2 max-w-2xl leading-relaxed">
                        Sampaikan pertanyaan atau laporan Anda melalui kanal pengaduan resmi Dinas Sosial
                        Kabupaten Mukomuko.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                    <a href="{{ route('pengaduan') }}"
                       class="inline-flex items-center justify-center gap-2 bg-white text-[#0E2650] px-6 py-3 rounded-md text-sm font-semibold hover:bg-slate-100 transition-colors">
                        <i class="fas fa-bullhorn text-xs"></i> Ajukan Pengaduan
                    </a>
                    <a href="{{ route('berita.index') }}"
                       class="inline-flex items-center justify-center gap-2 border border-white/30 text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-white/10 transition-colors">
                        Berita &amp; Pengumuman
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        (function () {
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
            }, { threshold: 0.15 });
            els.forEach(function (el) { io.observe(el); });
        })();
    </script>
@endsection
