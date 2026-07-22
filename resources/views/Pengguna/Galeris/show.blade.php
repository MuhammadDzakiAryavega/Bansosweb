@extends('layouts.layout')
@section('title', $galeri->judul_kegiatan . ' - Galeri Portal PKH Dinas Sosial Kabupaten Mukomuko')
@section('content')

@php $fotos = $galeri->fotos; @endphp

    <!-- ================= KEPALA HALAMAN ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

            <!-- Remah roti -->
            <nav aria-label="Remah roti" class="text-xs text-slate-500 mb-8">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:text-[#14346B] transition-colors">Beranda</a></li>
                    <li class="text-slate-300">/</li>
                    <li><a href="{{ route('galeri.index') }}" class="hover:text-[#14346B] transition-colors">Galeri</a></li>
                    <li class="text-slate-300">/</li>
                    <li class="text-slate-700 font-medium truncate max-w-[16rem] sm:max-w-md">{{ $galeri->judul_kegiatan }}</li>
                </ol>
            </nav>

            <div class="max-w-3xl">
                <a href="{{ route('galeri.index', ['tahun' => $galeri->tgl_pelaksanaan->format('Y')]) }}"
                   class="text-[11px] font-semibold px-3 py-1.5 rounded-md border bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20 hover:bg-[#14346B] hover:text-white transition-colors inline-block">
                    Kegiatan {{ $galeri->tgl_pelaksanaan->format('Y') }}
                </a>

                <h1 class="text-2xl md:text-3xl lg:text-[2.2rem] font-bold text-[#0E2650] leading-[1.25] tracking-tight mt-5">
                    {{ $galeri->judul_kegiatan }}
                </h1>

                <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>

                <dl class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <dt class="sr-only">Tanggal pelaksanaan</dt>
                        <dd>
                            <i class="far fa-calendar mr-1.5 text-slate-400"></i>
                            {{ $galeri->tgl_pelaksanaan->translatedFormat('l, d F Y') }}
                        </dd>
                    </div>
                    <div class="flex items-center gap-2">
                        <dt class="sr-only">Jumlah dokumentasi</dt>
                        <dd><i class="fas fa-camera mr-1.5 text-slate-400"></i>{{ $fotos->count() }} foto dokumentasi</dd>
                    </div>
                    <div class="flex items-center gap-2">
                        <dt class="sr-only">Penyelenggara</dt>
                        <dd><i class="far fa-building mr-1.5 text-slate-400"></i>Dinas Sosial Kab. Mukomuko</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <!-- ================= ISI ================= -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

                <article class="lg:col-span-8 space-y-6">

                    <!-- Deskripsi kegiatan -->
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                            <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Deskripsi Kegiatan</h2>
                        </div>
                        <div class="px-6 py-8 lg:px-10">
                            <div class="text-[15px] md:text-base text-slate-700 leading-[1.85] whitespace-pre-line">{{ $galeri->deskripsi_singkat }}</div>
                        </div>
                    </div>

                    <!-- Dokumentasi foto -->
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden"
                         x-data="{
                             terbuka: false,
                             aktif: 0,
                             fotos: @js($fotos->pluck('path')->values()),
                             buka(i) { this.aktif = i; this.terbuka = true; document.body.classList.add('overflow-hidden'); },
                             tutup() { this.terbuka = false; document.body.classList.remove('overflow-hidden'); },
                             maju() { this.aktif = (this.aktif + 1) % this.fotos.length; },
                             mundur() { this.aktif = (this.aktif - 1 + this.fotos.length) % this.fotos.length; }
                         }"
                         @keydown.window.escape="tutup()"
                         @keydown.window.arrow-right="terbuka && maju()"
                         @keydown.window.arrow-left="terbuka && mundur()">

                        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-4">
                            <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Media &amp; Dokumentasi</h2>
                            @if ($fotos->isNotEmpty())
                                <span class="text-xs text-slate-500">{{ $fotos->count() }} foto</span>
                            @endif
                        </div>

                        @if ($fotos->isNotEmpty())
                            <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($fotos as $foto)
                                    <button type="button" @click="buka({{ $loop->index }})"
                                            class="group relative rounded-md overflow-hidden border border-slate-200 hover:border-[#14346B] transition-colors">
                                        <img src="{{ $foto->path }}"
                                             alt="Dokumentasi {{ $galeri->judul_kegiatan }} foto ke-{{ $loop->iteration }}"
                                             loading="lazy"
                                             class="w-full aspect-[4/3] object-cover">
                                        <span class="absolute inset-0 bg-[#0E2650]/0 group-hover:bg-[#0E2650]/30 transition-colors flex items-center justify-center">
                                            <i class="fas fa-magnifying-glass-plus text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-16 text-center">
                                <span class="w-14 h-14 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-2xl mb-4">
                                    <i class="fas fa-images"></i>
                                </span>
                                <p class="font-semibold text-slate-800">Dokumentasi foto belum tersedia</p>
                                <p class="text-sm text-slate-500 mt-1">Foto kegiatan ini akan diunggah menyusul.</p>
                            </div>
                        @endif

                        <!-- Tampilan foto penuh -->
                        <div x-show="terbuka" x-cloak @click.self="tutup()"
                             class="fixed inset-0 z-[60] bg-slate-900/90 flex items-center justify-center p-4"
                             role="dialog" aria-modal="true" aria-label="Pratinjau foto dokumentasi">
                            <button type="button" @click="tutup()"
                                    class="absolute top-4 right-4 w-10 h-10 rounded-md border border-white/25 text-white hover:bg-white/10 transition-colors"
                                    aria-label="Tutup pratinjau">
                                <i class="fas fa-xmark"></i>
                            </button>

                            <button type="button" @click="mundur()" x-show="fotos.length > 1"
                                    class="absolute left-4 w-10 h-10 rounded-md border border-white/25 text-white hover:bg-white/10 transition-colors"
                                    aria-label="Foto sebelumnya">
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <figure class="max-w-4xl w-full text-center">
                                <img :src="fotos[aktif]" :alt="'Dokumentasi foto ke-' + (aktif + 1)"
                                     class="max-h-[75vh] w-auto mx-auto rounded-md">
                                <figcaption class="mt-4 text-sm text-blue-100/70">
                                    {{ $galeri->judul_kegiatan }}
                                    <span class="mx-2 text-white/30">|</span>
                                    Foto <span x-text="aktif + 1"></span> dari <span x-text="fotos.length"></span>
                                </figcaption>
                            </figure>

                            <button type="button" @click="maju()" x-show="fotos.length > 1"
                                    class="absolute right-4 w-10 h-10 rounded-md border border-white/25 text-white hover:bg-white/10 transition-colors"
                                    aria-label="Foto berikutnya">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <div class="px-6 py-5 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                            <p class="text-xs text-slate-500 leading-relaxed max-w-md">
                                Dokumentasi ini diterbitkan secara resmi oleh Dinas Sosial Kabupaten Mukomuko.
                                Untuk klarifikasi, silakan gunakan kanal pengaduan portal.
                            </p>
                            <a href="{{ route('galeri.index') }}"
                               class="group inline-flex items-center gap-2 text-sm font-semibold text-[#14346B] hover:text-[#0E2650] transition-colors">
                                <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
                                Kembali ke Galeri
                            </a>
                        </div>
                    </div>
                </article>

                <!-- ================= SAMPING ================= -->
                <aside class="lg:col-span-4 space-y-6">

                    <!-- Kegiatan lainnya -->
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                            <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kegiatan Lainnya</h2>
                        </div>

                        @forelse ($lainnya as $item)
                            @php $sampul = $item->sampul(); @endphp
                            <a href="{{ route('galeri.show', $item->slug) }}"
                               class="group flex gap-4 px-5 py-4 hover:bg-slate-50 transition-colors {{ ! $loop->last ? 'border-b border-slate-200' : '' }}">
                                @if ($sampul)
                                    <img src="{{ $sampul->path }}" alt=""
                                         class="w-20 h-16 object-cover rounded-md border border-slate-200 flex-shrink-0">
                                @else
                                    <span class="w-20 h-16 rounded-md border border-slate-200 bg-slate-50 text-slate-300 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-images text-sm"></i>
                                    </span>
                                @endif
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-slate-900 leading-snug group-hover:text-[#14346B] transition-colors">
                                        {{ \Illuminate\Support\Str::limit($item->judul_kegiatan, 60) }}
                                    </span>
                                    <span class="block text-xs text-slate-500 mt-1.5">
                                        {{ $item->tgl_pelaksanaan->translatedFormat('d M Y') }}
                                    </span>
                                </span>
                            </a>
                        @empty
                            <p class="px-5 py-6 text-sm text-slate-500 leading-relaxed">
                                Belum ada dokumentasi kegiatan lain.
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
                            <a href="{{ route('berita.index') }}" class="group flex items-center gap-3 px-5 py-4 hover:bg-slate-50 transition-colors">
                                <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0 group-hover:bg-[#14346B] group-hover:text-white transition-colors">
                                    <i class="fas fa-newspaper text-xs"></i>
                                </span>
                                <span class="text-sm font-semibold text-slate-800">Berita &amp; Pengumuman</span>
                            </a>
                            <a href="{{ route('pengaduan') }}" class="group flex items-center gap-3 px-5 py-4 hover:bg-slate-50 transition-colors">
                                <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0 group-hover:bg-[#14346B] group-hover:text-white transition-colors">
                                    <i class="fas fa-bullhorn text-xs"></i>
                                </span>
                                <span class="text-sm font-semibold text-slate-800">Ajukan Pengaduan</span>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

@endsection
