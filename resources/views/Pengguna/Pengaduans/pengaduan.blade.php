@extends('layouts.layout')
@section('title', 'Pengaduan Masyarakat - Portal PKH Dinas Sosial Kabupaten Mukomuko')
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
    $inputBase = 'w-full border rounded-md px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition';
    $inputOk   = 'border-slate-300 focus:border-[#14346B] focus:ring-[#14346B]';
    $inputErr  = 'border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E]';
@endphp

    <!-- ================= JUDUL HALAMAN ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <nav aria-label="Breadcrumb" class="text-xs text-slate-500">
                <ol class="flex items-center gap-2">
                    <li><a href="{{ url('/') }}" class="hover:text-[#14346B] transition-colors">Beranda</a></li>
                    <li class="text-slate-300">/</li>
                    <li class="text-slate-700 font-medium">Pengaduan Masyarakat</li>
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
                Pengaduan Masyarakat
            </h1>
            <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>
            <p class="mt-6 text-base text-slate-600 leading-relaxed max-w-3xl">
                Laporkan kendala penyaluran Program Keluarga Harapan (PKH) melalui formulir resmi berikut.
                Setiap laporan dicatat dan ditindaklanjuti oleh petugas Dinas Sosial Kabupaten Mukomuko.
            </p>

            <div class="mt-8 inline-flex border border-slate-200 rounded-md overflow-hidden bg-white text-sm">
                <a href="{{ route('pengaduan') }}" class="px-5 py-2.5 font-semibold bg-[#14346B] text-white">
                    Formulir Pengaduan
                </a>
                <a href="{{ route('pengaduan.riwayat') }}" class="px-5 py-2.5 font-semibold text-slate-600 border-l border-slate-200 hover:text-[#14346B] transition-colors">
                    Riwayat Pengaduan
                </a>
            </div>
        </div>
    </section>

    <!-- ================= FORMULIR ================= -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            @if (session('status') === 'pengaduan-terkirim')
                <div class="mb-6 border border-emerald-200 bg-emerald-50 rounded-md px-5 py-4 flex items-start gap-3">
                    <i class="fas fa-circle-check text-emerald-600 mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-emerald-800 text-sm">Pengaduan berhasil dikirim.</p>
                        <p class="text-sm text-emerald-700 mt-0.5">
                            Laporan Anda tercatat dengan status <strong>Baru</strong> dan akan ditindaklanjuti petugas.
                            Perkembangannya dapat dipantau pada <a href="{{ route('pengaduan.riwayat') }}" class="underline font-semibold">Riwayat Pengaduan</a>.
                        </p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 border border-rose-200 bg-rose-50 rounded-md px-5 py-4">
                    <p class="text-sm text-[#C8102E] font-semibold flex items-start gap-2 mb-1">
                        <i class="fas fa-circle-exclamation mt-0.5"></i>
                        <span>Pengaduan belum dapat dikirim. Periksa kembali isian berikut:</span>
                    </p>
                    <ul class="text-sm text-[#C8102E]/90 list-disc pl-9 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pengaduan.store') }}" enctype="multipart/form-data"
                  class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                @csrf

                <div class="lg:col-span-8 space-y-6">

                    <!-- Bagian 01: Data pelapor -->
                    <div class="reveal bg-white border border-slate-200 rounded-lg">
                        <div class="flex items-center gap-3 px-7 py-5 border-b border-slate-200">
                            <span class="w-9 h-9 flex items-center justify-center rounded-md border border-[#14346B]/20 text-[#14346B] font-bold text-sm flex-shrink-0">01</span>
                            <div>
                                <h2 class="font-semibold text-slate-900">Data Pelapor</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Isi identitas sesuai dokumen kependudukan agar mudah dihubungi.</p>
                            </div>
                        </div>

                        <div class="px-7 py-6 space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="nama_pengadu" class="block text-sm font-semibold text-slate-700 mb-2">
                                        Nama Lengkap <span class="text-[#C8102E]">*</span>
                                    </label>
                                    <input type="text" id="nama_pengadu" name="nama_pengadu" required autocomplete="name"
                                           value="{{ old('nama_pengadu', Auth::user()->name) }}" placeholder="Nama sesuai KTP"
                                           class="{{ $inputBase }} {{ $errors->has('nama_pengadu') ? $inputErr : $inputOk }}">
                                    @error('nama_pengadu')
                                        <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email_pengadu" class="block text-sm font-semibold text-slate-700 mb-2">
                                        Alamat Surel <span class="text-[#C8102E]">*</span>
                                    </label>
                                    <input type="email" id="email_pengadu" name="email_pengadu" required autocomplete="email"
                                           value="{{ old('email_pengadu', Auth::user()->email) }}" placeholder="contoh@mail.com"
                                           class="{{ $inputBase }} {{ $errors->has('email_pengadu') ? $inputErr : $inputOk }}">
                                    @error('email_pengadu')
                                        <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="no_hp_pengadu" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Nomor Telepon / WhatsApp
                                    <span class="text-slate-400 font-normal">(opsional)</span>
                                </label>
                                <input type="text" id="no_hp_pengadu" name="no_hp_pengadu" inputmode="tel"
                                       value="{{ old('no_hp_pengadu') }}" placeholder="Contoh: 081234567890"
                                       class="{{ $inputBase }} {{ $errors->has('no_hp_pengadu') ? $inputErr : $inputOk }}">
                                @error('no_hp_pengadu')
                                    <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="alamat_pengadu" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Alamat Lengkap <span class="text-[#C8102E]">*</span>
                                </label>
                                <textarea id="alamat_pengadu" name="alamat_pengadu" rows="3" required
                                          placeholder="Contoh: Dusun/RT/RW, Desa, Kecamatan Teramang Jaya"
                                          class="{{ $inputBase }} resize-y {{ $errors->has('alamat_pengadu') ? $inputErr : $inputOk }}">{{ old('alamat_pengadu') }}</textarea>
                                @error('alamat_pengadu')
                                    <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 02: Uraian pengaduan -->
                    <div class="reveal bg-white border border-slate-200 rounded-lg" style="transition-delay:80ms">
                        <div class="flex items-center gap-3 px-7 py-5 border-b border-slate-200">
                            <span class="w-9 h-9 flex items-center justify-center rounded-md border border-[#14346B]/20 text-[#14346B] font-bold text-sm flex-shrink-0">02</span>
                            <div>
                                <h2 class="font-semibold text-slate-900">Uraian Pengaduan</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Jelaskan kendala yang dialami selengkap mungkin.</p>
                            </div>
                        </div>

                        <div class="px-7 py-6 space-y-5">
                            <div>
                                <label for="judul_pengaduan" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Judul Pengaduan <span class="text-[#C8102E]">*</span>
                                </label>
                                <input type="text" id="judul_pengaduan" name="judul_pengaduan" required
                                       value="{{ old('judul_pengaduan') }}" placeholder="Contoh: Bantuan PKH belum cair"
                                       class="{{ $inputBase }} {{ $errors->has('judul_pengaduan') ? $inputErr : $inputOk }}">
                                @error('judul_pengaduan')
                                    <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="isi_pengaduan" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Isi Pengaduan <span class="text-[#C8102E]">*</span>
                                </label>
                                <textarea id="isi_pengaduan" name="isi_pengaduan" rows="7" required
                                          placeholder="Uraikan kronologi atau kendala yang Anda alami, termasuk waktu kejadian dan pihak yang terkait."
                                          class="{{ $inputBase }} resize-y {{ $errors->has('isi_pengaduan') ? $inputErr : $inputOk }}">{{ old('isi_pengaduan') }}</textarea>
                                @error('isi_pengaduan')
                                    <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <span class="block text-sm font-semibold text-slate-700 mb-2">
                                    Bukti Pendukung <span class="text-slate-400 font-normal">(opsional)</span>
                                </span>
                                <label for="lampiran"
                                       class="flex flex-col items-center justify-center gap-2 border border-dashed rounded-md p-8 text-center cursor-pointer hover:border-[#14346B] hover:bg-slate-50 transition-colors {{ $errors->has('lampiran') ? 'border-rose-300' : 'border-slate-300' }}">
                                    <span class="w-10 h-10 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center">
                                        <i class="fas fa-paperclip text-sm"></i>
                                    </span>
                                    <span class="text-sm text-slate-600">
                                        <span class="font-semibold text-[#14346B]">Pilih berkas</span> foto atau dokumen pendukung
                                    </span>
                                    <span class="text-xs text-slate-400">Format JPG, PNG, atau PDF &mdash; maksimal 2 MB</span>
                                    <span id="file-name" class="text-xs font-semibold text-[#14346B]"></span>
                                    <input id="lampiran" type="file" name="lampiran" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                                </label>
                                @error('lampiran')
                                    <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="px-7 py-5 border-t border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center gap-3">
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                                <i class="fas fa-paper-plane text-xs"></i> Kirim Pengaduan
                            </button>
                            <a href="{{ route('pengaduan.riwayat') }}"
                               class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-6 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                                Lihat Riwayat
                            </a>
                            <p class="text-xs text-slate-500 sm:ml-auto">
                                Tanda <span class="text-[#C8102E]">*</span> wajib diisi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Panel keterangan -->
                <aside class="lg:col-span-4 space-y-6">
                    <div class="reveal bg-white border border-slate-200 rounded-lg overflow-hidden" style="transition-delay:40ms">
                        <div class="bg-[#14346B] px-6 py-4">
                            <h2 class="text-white font-semibold text-base flex items-center gap-2">
                                <i class="fas fa-circle-info text-sm opacity-80"></i> Ketentuan Pengaduan
                            </h2>
                        </div>
                        <ul class="divide-y divide-slate-100 text-sm text-slate-600">
                            @php
                                $ketentuan = [
                                    'Pengaduan diisi dengan data yang benar dan dapat dipertanggungjawabkan.',
                                    'Satu laporan untuk satu pokok permasalahan agar mudah ditindaklanjuti.',
                                    'Lampirkan bukti pendukung bila tersedia untuk mempercepat verifikasi.',
                                    'Status laporan diperbarui petugas dan dapat dipantau melalui menu Riwayat.',
                                ];
                            @endphp
                            @foreach($ketentuan as $index => $poin)
                            <li class="flex items-start gap-3 px-6 py-4 leading-relaxed">
                                <span class="text-xs font-bold text-[#14346B]/50 mt-0.5 flex-shrink-0">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                {{ $poin }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="reveal border border-slate-200 rounded-lg bg-white px-6 py-5" style="transition-delay:120ms">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-4">Layanan Tatap Muka</h3>
                        <dl class="text-sm divide-y divide-slate-100 mb-4">
                            <div class="flex justify-between py-2">
                                <dt class="text-slate-600">Senin &ndash; Kamis</dt>
                                <dd class="font-semibold text-slate-800">08.00 &ndash; 16.00 WIB</dd>
                            </div>
                            <div class="flex justify-between py-2">
                                <dt class="text-slate-600">Jumat</dt>
                                <dd class="font-semibold text-slate-800">08.00 &ndash; 16.30 WIB</dd>
                            </div>
                            <div class="flex justify-between py-2">
                                <dt class="text-slate-600">Sabtu &ndash; Minggu</dt>
                                <dd class="font-semibold text-[#C8102E]">Libur</dd>
                            </div>
                        </dl>
                        <div class="flex items-start gap-3 text-xs text-slate-500 leading-relaxed border-t border-slate-100 pt-4">
                            <i class="fas fa-location-dot text-[#14346B]/60 mt-0.5 w-4 text-center flex-shrink-0"></i>
                            <span>Jl. Imam Bonjol, Komplek Perkantoran Pemda, Kel. Bandar Ratu, Kec. Kota Mukomuko, Bengkulu 38714</span>
                        </div>
                    </div>
                </aside>
            </form>
        </div>
    </section>

    <script>
        (function () {
            // Fade-in halus saat elemen masuk viewport
            var els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) {
                els.forEach(function (el) { el.classList.add('is-visible'); });
            } else {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            io.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });
                els.forEach(function (el) { io.observe(el); });
            }

            // Tampilkan nama berkas yang dipilih
            var input = document.getElementById('lampiran');
            var label = document.getElementById('file-name');
            if (input && label) {
                input.addEventListener('change', function () {
                    label.textContent = input.files.length ? 'Terpilih: ' + input.files[0].name : '';
                });
            }
        })();
    </script>
@endsection
