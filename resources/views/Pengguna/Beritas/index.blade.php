@extends('layouts.layout')
@section('title', 'Berita & Pengumuman - Portal PKH Dinas Sosial Kabupaten Mukomuko')
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
    $adaFilter = request()->filled('cari') || request()->filled('kategori');
    $tampilSorotan = $sorotan && ! $adaFilter && $beritas->currentPage() === 1;
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
                    Informasi Publik
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-end">
                <div class="lg:col-span-7">
                    <h1 class="text-3xl md:text-4xl font-bold text-[#0E2650] leading-[1.2] tracking-tight">
                        Berita &amp; Pengumuman
                    </h1>
                    <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>
                    <p class="mt-6 text-base text-slate-600 leading-relaxed max-w-2xl">
                        Informasi resmi seputar penyaluran bantuan, kegiatan, dan pengumuman Program Keluarga Harapan
                        Kecamatan Teramang Jaya yang diterbitkan oleh Dinas Sosial Kabupaten Mukomuko.
                    </p>
                </div>

                <div class="lg:col-span-5">
                    <form method="GET" action="{{ route('berita.index') }}" class="flex gap-2">
                        @if (request('kategori'))
                            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                        @endif
                        <label for="cari" class="sr-only">Cari berita</label>
                        <div class="relative flex-1">
                            <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" id="cari" name="cari" value="{{ request('cari') }}"
                                   placeholder="Cari judul atau isi berita..."
                                   class="w-full pl-10 pr-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                        </div>
                        <button type="submit"
                                class="bg-[#14346B] text-white px-5 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors flex-shrink-0">
                            Cari
                        </button>
                    </form>
                </div>
            </div>

            <!-- Saringan kategori -->
            <div class="mt-8 flex flex-wrap gap-2">
                <a href="{{ route('berita.index', request('cari') ? ['cari' => request('cari')] : []) }}"
                   class="px-4 py-2 rounded-md text-sm font-semibold border transition-colors {{ ! request('kategori') ? 'bg-[#14346B] text-white border-[#14346B]' : 'border-slate-300 text-slate-600 hover:border-[#14346B] hover:text-[#14346B]' }}">
                    Semua
                </a>
                @foreach ($kategoriList as $kategori)
                    <a href="{{ route('berita.index', array_filter(['kategori' => $kategori, 'cari' => request('cari')])) }}"
                       class="px-4 py-2 rounded-md text-sm font-semibold border transition-colors {{ request('kategori') === $kategori ? 'bg-[#14346B] text-white border-[#14346B]' : 'border-slate-300 text-slate-600 hover:border-[#14346B] hover:text-[#14346B]' }}">
                        {{ $kategori }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ================= DAFTAR BERITA ================= -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">

            @if ($adaFilter)
                <p class="text-sm text-slate-600 mb-6">
                    Menampilkan <span class="font-semibold text-slate-800">{{ $beritas->total() }}</span> berita
                    @if (request('kategori')) pada kategori <span class="font-semibold text-slate-800">{{ request('kategori') }}</span> @endif
                    @if (request('cari')) untuk kata kunci <span class="font-semibold text-slate-800">"{{ request('cari') }}"</span> @endif
                    &middot;
                    <a href="{{ route('berita.index') }}" class="font-semibold text-[#14346B] hover:underline">Atur ulang</a>
                </p>
            @endif

            <!-- Berita sorotan -->
            @if ($tampilSorotan)
                <a href="{{ route('berita.show', $sorotan->slug) }}"
                   class="reveal group grid grid-cols-1 lg:grid-cols-12 bg-white border border-slate-200 rounded-lg overflow-hidden mb-8 hover:border-[#14346B] transition-colors">
                    <div class="lg:col-span-6 bg-slate-100">
                        @if ($sorotan->gambar_cover)
                            <img src="{{ $sorotan->gambar_cover }}" alt="{{ $sorotan->judul_berita }}"
                                 class="w-full h-56 lg:h-full object-cover">
                        @else
                            <div class="w-full h-56 lg:h-full min-h-[14rem] flex items-center justify-center text-slate-300 text-3xl">
                                <i class="fas fa-newspaper"></i>
                            </div>
                        @endif
                    </div>
                    <div class="lg:col-span-6 p-6 lg:p-8 flex flex-col justify-center">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20">
                                {{ $sorotan->kategori }}
                            </span>
                            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#C8102E]">Terbaru</span>
                        </div>
                        <h2 class="text-xl md:text-2xl font-bold text-[#0E2650] leading-snug tracking-tight">
                            {{ $sorotan->judul_berita }}
                        </h2>
                        <div class="w-12 h-1 bg-[#C8102E] mt-5"></div>
                        <p class="mt-5 text-sm text-slate-600 leading-relaxed">{{ $sorotan->ringkasan(220) }}</p>
                        <p class="mt-5 text-xs text-slate-500">
                            <i class="far fa-calendar mr-1.5"></i>
                            {{ $sorotan->tanggalTampil()->translatedFormat('d F Y') }}
                            <span class="mx-2 text-slate-300">|</span>
                            <i class="far fa-user mr-1.5"></i>{{ $sorotan->penulis }}
                        </p>
                        <span class="mt-5 text-sm font-semibold text-[#14346B] inline-flex items-center gap-2">
                            Baca selengkapnya
                            <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                        </span>
                    </div>
                </a>
            @endif

            <!-- Kartu berita -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($beritas as $item)
                    @if ($tampilSorotan && $item->id_berita === $sorotan->id_berita)
                        @continue
                    @endif
                    <a href="{{ route('berita.show', $item->slug) }}"
                       class="reveal group bg-white border border-slate-200 rounded-lg overflow-hidden hover:border-[#14346B] transition-colors flex flex-col"
                       style="transition-delay: {{ $loop->index * 60 }}ms">
                        <div class="aspect-[16/9] bg-slate-100 overflow-hidden">
                            @if ($item->gambar_cover)
                                <img src="{{ $item->gambar_cover }}" alt="{{ $item->judul_berita }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-2xl">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20 self-start">
                                {{ $item->kategori }}
                            </span>
                            <h3 class="font-semibold text-slate-900 mt-4 leading-snug">{{ $item->judul_berita }}</h3>
                            <p class="text-sm text-slate-500 leading-relaxed mt-3 flex-grow">{{ $item->ringkasan(110) }}</p>
                            <p class="text-xs text-slate-500 mt-4 pt-4 border-t border-slate-100">
                                <i class="far fa-calendar mr-1.5"></i>
                                {{ $item->tanggalTampil()->translatedFormat('d M Y') }}
                                <span class="mx-2 text-slate-300">|</span>
                                {{ $item->penulis }}
                            </p>
                            <span class="mt-4 text-sm font-semibold text-[#14346B] inline-flex items-center gap-2">
                                Baca selengkapnya
                                <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 bg-white border border-slate-200 rounded-lg px-6 py-16 text-center">
                        <span class="w-14 h-14 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-2xl mb-4">
                            <i class="fas fa-newspaper"></i>
                        </span>
                        <p class="font-semibold text-slate-800">Belum ada berita yang tersedia</p>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ $adaFilter
                                ? 'Coba ubah kata kunci atau pilih kategori lain.'
                                : 'Informasi terbaru dari Dinas Sosial akan tampil di halaman ini.' }}
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($beritas->hasPages())
                <div class="mt-10">
                    {{ $beritas->links() }}
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
                    <a href="{{ route('tentang') }}"
                       class="inline-flex items-center justify-center gap-2 border border-white/30 text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-white/10 transition-colors">
                        Profil Dinas
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
