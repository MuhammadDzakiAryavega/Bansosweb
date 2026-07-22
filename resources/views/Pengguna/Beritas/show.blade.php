@extends('layouts.layout')
@section('title', $berita->judul_berita . ' - Portal PKH Dinas Sosial Kabupaten Mukomuko')
@section('content')

    <!-- ================= KEPALA ARTIKEL ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

            <!-- Remah roti -->
            <nav aria-label="Remah roti" class="text-xs text-slate-500 mb-8">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:text-[#14346B] transition-colors">Beranda</a></li>
                    <li class="text-slate-300">/</li>
                    <li><a href="{{ route('berita.index') }}" class="hover:text-[#14346B] transition-colors">Berita</a></li>
                    <li class="text-slate-300">/</li>
                    <li class="text-slate-700 font-medium truncate max-w-[16rem] sm:max-w-md">{{ $berita->judul_berita }}</li>
                </ol>
            </nav>

            <div class="max-w-3xl">
                <a href="{{ route('berita.index', ['kategori' => $berita->kategori]) }}"
                   class="text-[11px] font-semibold px-3 py-1.5 rounded-md border bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20 hover:bg-[#14346B] hover:text-white transition-colors inline-block">
                    {{ $berita->kategori }}
                </a>

                <h1 class="text-2xl md:text-3xl lg:text-[2.2rem] font-bold text-[#0E2650] leading-[1.25] tracking-tight mt-5">
                    {{ $berita->judul_berita }}
                </h1>

                <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>

                <dl class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <dt class="sr-only">Penulis</dt>
                        <dd><i class="far fa-user mr-1.5 text-slate-400"></i>{{ $berita->penulis }}</dd>
                    </div>
                    <div class="flex items-center gap-2">
                        <dt class="sr-only">Tanggal terbit</dt>
                        <dd>
                            <i class="far fa-calendar mr-1.5 text-slate-400"></i>
                            {{ $berita->tanggalTampil()->translatedFormat('d F Y, H:i') }} WIB
                        </dd>
                    </div>
                    <div class="flex items-center gap-2">
                        <dt class="sr-only">Penerbit</dt>
                        <dd><i class="far fa-building mr-1.5 text-slate-400"></i>Dinas Sosial Kab. Mukomuko</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <!-- ================= ISI ARTIKEL ================= -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

                <article class="lg:col-span-8">
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                        @if ($berita->gambar_cover)
                            <figure>
                                <img src="{{ $berita->gambar_cover }}" alt="{{ $berita->judul_berita }}"
                                     class="w-full aspect-[16/9] object-cover border-b border-slate-200">
                                <figcaption class="px-6 py-3 text-xs text-slate-500 border-b border-slate-200 bg-slate-50">
                                    Dokumentasi Dinas Sosial Kabupaten Mukomuko
                                </figcaption>
                            </figure>
                        @endif

                        <div class="px-6 py-8 lg:px-10 lg:py-10">
                            <div class="text-[15px] md:text-base text-slate-700 leading-[1.85] whitespace-pre-line">{{ $berita->isi_artikel }}</div>

                            <div class="mt-10 pt-6 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                                <p class="text-xs text-slate-500 leading-relaxed max-w-md">
                                    Informasi ini diterbitkan secara resmi oleh Dinas Sosial Kabupaten Mukomuko.
                                    Untuk klarifikasi, silakan gunakan kanal pengaduan portal.
                                </p>
                                <a href="{{ route('berita.index') }}"
                                   class="group inline-flex items-center gap-2 text-sm font-semibold text-[#14346B] hover:text-[#0E2650] transition-colors">
                                    <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
                                    Kembali ke Daftar Berita
                                </a>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- ================= SAMPING ================= -->
                <aside class="lg:col-span-4 space-y-6">

                    <!-- Berita terkait -->
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                            <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Berita Terkait</h2>
                        </div>

                        @forelse ($terkait as $item)
                            <a href="{{ route('berita.show', $item->slug) }}"
                               class="group flex gap-4 px-5 py-4 hover:bg-slate-50 transition-colors {{ ! $loop->last ? 'border-b border-slate-200' : '' }}">
                                @if ($item->gambar_cover)
                                    <img src="{{ $item->gambar_cover }}" alt=""
                                         class="w-20 h-16 object-cover rounded-md border border-slate-200 flex-shrink-0">
                                @else
                                    <span class="w-20 h-16 rounded-md border border-slate-200 bg-slate-50 text-slate-300 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-newspaper text-sm"></i>
                                    </span>
                                @endif
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-slate-900 leading-snug group-hover:text-[#14346B] transition-colors">
                                        {{ \Illuminate\Support\Str::limit($item->judul_berita, 60) }}
                                    </span>
                                    <span class="block text-xs text-slate-500 mt-1.5">
                                        {{ $item->tanggalTampil()->translatedFormat('d M Y') }}
                                    </span>
                                </span>
                            </a>
                        @empty
                            <p class="px-5 py-6 text-sm text-slate-500 leading-relaxed">
                                Belum ada berita lain pada kategori {{ $berita->kategori }}.
                            </p>
                        @endforelse
                    </div>

                    <!-- Tautan layanan -->
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                        <div class="bg-[#14346B] px-5 py-4">
                            <h2 class="text-white font-semibold text-sm flex items-center gap-2">
                                <i class="fas fa-headset text-xs opacity-80"></i> Butuh Bantuan?
                            </h2>
                            <p class="text-blue-100/80 text-xs mt-1">Layanan portal PKH</p>
                        </div>
                        <div class="divide-y divide-slate-200">
                            <a href="{{ url('/') }}#cek-nik" class="group flex items-center gap-3 px-5 py-4 hover:bg-slate-50 transition-colors">
                                <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0 group-hover:bg-[#14346B] group-hover:text-white transition-colors">
                                    <i class="fas fa-magnifying-glass text-xs"></i>
                                </span>
                                <span class="text-sm font-semibold text-slate-800">Cek Status Kepesertaan</span>
                            </a>
                            <a href="{{ route('pengaduan') }}" class="group flex items-center gap-3 px-5 py-4 hover:bg-slate-50 transition-colors">
                                <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0 group-hover:bg-[#14346B] group-hover:text-white transition-colors">
                                    <i class="fas fa-bullhorn text-xs"></i>
                                </span>
                                <span class="text-sm font-semibold text-slate-800">Ajukan Pengaduan</span>
                            </a>
                            <a href="{{ route('tentang') }}" class="group flex items-center gap-3 px-5 py-4 hover:bg-slate-50 transition-colors">
                                <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0 group-hover:bg-[#14346B] group-hover:text-white transition-colors">
                                    <i class="fas fa-circle-info text-xs"></i>
                                </span>
                                <span class="text-sm font-semibold text-slate-800">Informasi Program</span>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

@endsection
