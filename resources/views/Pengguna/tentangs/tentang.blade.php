@extends('layouts.layout')
@section('title', 'Profil Dinas - Portal PKH Dinas Sosial Kabupaten Mukomuko')
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
                    <li class="text-slate-700 font-medium">Profil Dinas</li>
                </ol>
            </nav>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">
                <div class="lg:col-span-7">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-block w-7 border border-slate-300 overflow-hidden">
                            <span class="block h-[7px] bg-[#C8102E]"></span>
                            <span class="block h-[7px] bg-white"></span>
                        </span>
                        <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                            Profil Instansi
                        </span>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-bold text-[#0E2650] leading-[1.2] tracking-tight">
                        Dinas Sosial
                        <span class="block">Kabupaten Mukomuko</span>
                    </h1>

                    <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>

                    <p class="mt-6 text-base md:text-[1.05rem] text-slate-600 leading-relaxed max-w-2xl">
                        Platform digital resmi Kabupaten Mukomuko yang mendukung penyaluran Program Keluarga
                        Harapan (PKH) secara transparan, adil, dan tepat sasaran melalui penilaian objektif
                        berbasis metode <span class="font-semibold text-slate-800">Simple Additive Weighting (SAW)</span>.
                    </p>
                </div>

                <!-- Ringkasan instansi -->
                <div class="lg:col-span-5 w-full">
                    <div class="border border-slate-200 rounded-lg overflow-hidden">
                        <div class="bg-[#14346B] px-6 py-4">
                            <h2 class="text-white font-semibold text-base flex items-center gap-2">
                                <i class="fas fa-building-columns text-sm opacity-80"></i> Identitas Instansi
                            </h2>
                        </div>
                        <dl class="divide-y divide-slate-200 text-sm">
                            @php
                                $identitas = [
                                    ['label' => 'Nama Instansi', 'value' => 'Dinas Sosial Kabupaten Mukomuko'],
                                    ['label' => 'Pemerintah Daerah', 'value' => 'Kabupaten Mukomuko, Provinsi Bengkulu'],
                                    ['label' => 'Program', 'value' => 'Program Keluarga Harapan (PKH)'],
                                    ['label' => 'Surel', 'value' => 'dinassosialkabmukomuko@gmail.com'],
                                ];
                            @endphp
                            @foreach($identitas as $row)
                            <div class="px-6 py-3.5 sm:flex sm:items-start sm:gap-4">
                                <dt class="text-slate-500 sm:w-40 flex-shrink-0">{{ $row['label'] }}</dt>
                                <dd class="font-semibold text-slate-800 mt-0.5 sm:mt-0 break-words">{{ $row['value'] }}</dd>
                            </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= TENTANG SISTEM ================= -->
    <section class="bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
            <div class="mb-8">
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Mengenal Sistem Kami</span>
                <h2 class="text-2xl md:text-3xl font-bold text-[#0E2650] mt-2">Inovasi Digital untuk Kesejahteraan Bersama</h2>
                <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
                <div class="reveal bg-white border border-slate-200 rounded-lg p-7 lg:p-8">
                    <p class="text-slate-600 leading-relaxed mb-4">
                        Sistem Pendukung Keputusan (SPK) Penerima PKH ini dikembangkan sebagai bentuk komitmen
                        Pemerintah Kabupaten Mukomuko dalam mengelola data kesejahteraan sosial yang akurat.
                    </p>
                    <p class="text-slate-600 leading-relaxed">
                        Dengan mengimplementasikan metode komputasi <strong class="text-slate-800">Simple Additive
                        Weighting (SAW)</strong>, sistem ini menyeleksi dan meranking data calon penerima PKH
                        berdasarkan kriteria yang objektif. Hal ini meminimalisir kesalahan penyaluran
                        (<em>human error</em>) dan mencegah terjadinya tumpang tindih data penerima bantuan.
                    </p>
                </div>

                <div class="reveal bg-white border border-slate-200 rounded-lg divide-y divide-slate-200" style="transition-delay:80ms">
                    <div class="p-7 lg:p-8">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-eye text-sm"></i>
                            </span>
                            <h3 class="text-lg font-bold text-[#0E2650]">Visi</h3>
                        </div>
                        <p class="text-slate-600 leading-relaxed text-sm">
                            Terwujudnya Masyarakat Kabupaten Mukomuko Yang Maju, Mandiri, Berkarakter, dan Sejahtera
                            Berbasis Agro, Perikanan, dan Berilmu Pengetahuan dan Teknologi (IPTEK) Serta Beriman
                            dan Bertakwa (IMTAQ).
                        </p>
                    </div>
                    <div class="p-7 lg:p-8">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-bullseye text-sm"></i>
                            </span>
                            <h3 class="text-lg font-bold text-[#0E2650]">Misi</h3>
                        </div>
                        <p class="text-slate-600 leading-relaxed text-sm">
                            Menjadikan Kabupaten Mukomuko Sebagai Pusat Agroindustri, Perdagangan, dan Kelautan
                            Dengan Cara Meningkatkan Pemerataan Kesejahteraan, Pelayanan dan Perlindungan Sosial
                            Bagi Masyarakat.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= NILAI UTAMA ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
            <div class="mb-8">
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Prinsip Pelayanan</span>
                <h2 class="text-2xl md:text-3xl font-bold text-[#0E2650] mt-2">Nilai Utama Pelayanan</h2>
                <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                <p class="mt-5 text-slate-600 max-w-2xl leading-relaxed text-sm md:text-base">
                    Prinsip dasar yang dipegang teguh dalam menjalankan sistem pelayanan PKH pada portal ini.
                </p>
            </div>

            @php
                $nilai = [
                    [
                        'icon'  => 'fa-hands-helping',
                        'title' => 'Tepat Sasaran',
                        'desc'  => 'Penyaluran difokuskan secara akurat kepada masyarakat rentan dan prasejahtera sesuai hasil pembobotan data lapangan.',
                    ],
                    [
                        'icon'  => 'fa-scale-balanced',
                        'title' => 'Adil & Objektif',
                        'desc'  => 'Seluruh calon penerima memiliki hak yang sama untuk dinilai oleh sistem tanpa adanya intervensi pihak tertentu.',
                    ],
                    [
                        'icon'  => 'fa-lock-open',
                        'title' => 'Transparan',
                        'desc'  => 'Informasi kriteria, metode penilaian, hingga daftar penerima disajikan secara terbuka untuk diawasi publik.',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-slate-200 border border-slate-200 rounded-lg overflow-hidden">
                @foreach($nilai as $index => $item)
                <div class="reveal bg-white p-6 lg:p-7" style="transition-delay: {{ $index * 80 }}ms">
                    <span class="w-10 h-10 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center mb-5">
                        <i class="fas {{ $item['icon'] }} text-sm"></i>
                    </span>
                    <h3 class="font-semibold text-slate-900 mb-2">{{ $item['title'] }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $item['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ================= METODE & WAKTU PELAYANAN ================= -->
    <section class="bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
            <div class="mb-8">
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Akses Layanan</span>
                <h2 class="text-2xl md:text-3xl font-bold text-[#0E2650] mt-2">Metode &amp; Waktu Pelayanan</h2>
                <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                <p class="mt-5 text-slate-600 max-w-3xl leading-relaxed text-sm md:text-base">
                    Layanan PKH dapat diakses kapan saja secara daring melalui portal ini, atau secara langsung
                    dengan mendatangi kantor pada jam kerja.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">

                <!-- ONLINE -->
                <div class="reveal bg-white border border-slate-200 rounded-lg overflow-hidden">
                    <div class="flex items-center justify-between px-7 py-5 border-b border-slate-200">
                        <h3 class="font-semibold text-slate-900 flex items-center gap-3">
                            <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-globe text-sm"></i>
                            </span>
                            Pelayanan Daring
                        </h3>
                        <span class="inline-flex items-center gap-2 border border-emerald-200 bg-emerald-50 text-emerald-700 text-[11px] font-semibold px-3 py-1.5 rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 24 JAM
                        </span>
                    </div>
                    <div class="px-7 py-6">
                        <p class="text-sm text-slate-500 leading-relaxed mb-5">
                            Akses mandiri melalui portal ini kapan saja tanpa perlu datang ke kantor.
                        </p>
                        <ul class="text-sm text-slate-600 divide-y divide-slate-100 border-y border-slate-100 mb-6">
                            <li class="flex items-start gap-3 py-3">
                                <i class="fas fa-id-card text-[#14346B]/60 mt-0.5 w-4 text-center flex-shrink-0"></i>
                                Cek status penerima PKH menggunakan NIK
                            </li>
                            <li class="flex items-start gap-3 py-3">
                                <i class="fas fa-bullhorn text-[#14346B]/60 mt-0.5 w-4 text-center flex-shrink-0"></i>
                                Melaporkan kendala lewat menu pengaduan
                            </li>
                            <li class="flex items-start gap-3 py-3">
                                <i class="fas fa-circle-info text-[#14346B]/60 mt-0.5 w-4 text-center flex-shrink-0"></i>
                                Membaca informasi program dan tahapan penilaian
                            </li>
                        </ul>
                        <a href="{{ url('/') }}#cek-nik" class="inline-flex items-center gap-2 text-sm font-semibold text-[#14346B] hover:gap-3 transition-all">
                            Cek Status Sekarang <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- OFFLINE -->
                <div class="reveal bg-white border border-slate-200 rounded-lg overflow-hidden" style="transition-delay:80ms">
                    <div class="flex items-center justify-between px-7 py-5 border-b border-slate-200">
                        <h3 class="font-semibold text-slate-900 flex items-center gap-3">
                            <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-building text-sm"></i>
                            </span>
                            Pelayanan Tatap Muka
                        </h3>
                        <span id="office-status" class="inline-flex items-center gap-2 border border-slate-200 bg-slate-50 text-slate-500 text-[11px] font-semibold px-3 py-1.5 rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Memeriksa&hellip;
                        </span>
                    </div>
                    <div class="px-7 py-6">
                        <p class="text-sm text-slate-500 leading-relaxed mb-5">
                            Datang langsung ke kantor untuk pelayanan tatap muka pada jadwal berikut:
                        </p>
                        <dl class="text-sm divide-y divide-slate-100 border-y border-slate-100 mb-6">
                            <div class="flex justify-between py-3">
                                <dt class="text-slate-600">Senin &ndash; Kamis</dt>
                                <dd class="font-semibold text-slate-800">08.00 &ndash; 16.00 WIB</dd>
                            </div>
                            <div class="flex justify-between py-3">
                                <dt class="text-slate-600">Jumat</dt>
                                <dd class="font-semibold text-slate-800">08.00 &ndash; 16.30 WIB</dd>
                            </div>
                            <div class="flex justify-between py-3">
                                <dt class="text-slate-600">Sabtu &ndash; Minggu</dt>
                                <dd class="font-semibold text-[#C8102E]">Libur</dd>
                            </div>
                        </dl>
                        <div class="flex items-start gap-3 text-sm text-slate-500 leading-relaxed">
                            <i class="fas fa-location-dot text-[#14346B]/60 mt-1 w-4 text-center flex-shrink-0"></i>
                            <span>
                                Jl. Imam Bonjol, Komplek Perkantoran Pemerintah Daerah Kabupaten Mukomuko,
                                Kel. Bandar Ratu, Kec. Kota Mukomuko, Provinsi Bengkulu.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= STRUKTUR ORGANISASI ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
            <div class="mb-8">
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Organisasi</span>
                <h2 class="text-2xl md:text-3xl font-bold text-[#0E2650] mt-2">Struktur Organisasi</h2>
                <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                <p class="mt-5 text-slate-600 max-w-2xl leading-relaxed text-sm md:text-base">
                    Susunan organisasi Dinas Sosial Kabupaten Mukomuko berdasarkan tugas pokok dan fungsi.
                </p>
            </div>

            <div class="border border-slate-200 rounded-lg bg-white p-6 overflow-x-auto">
                <div class="relative w-[1200px] min-w-[1200px] h-[780px] mx-auto">
                    <!-- Garis penghubung -->
                    <div class="absolute bg-slate-300 w-[2px]" style="left:599px; top:70px; height:620px;"></div>
                    <div class="absolute border-t-2 border-dashed border-slate-300" style="left:190px; top:114px; width:710px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:189px; top:115px; height:35px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:899px; top:115px; height:25px;"></div>
                    <div class="absolute border-t-2 border-dashed border-slate-300" style="left:300px; top:324px; width:600px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:299px; top:325px; height:15px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:899px; top:325px; height:15px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:899px; top:198px; height:15px;"></div>
                    <div class="absolute bg-slate-300 h-[2px]" style="left:795px; top:212px; width:210px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:794px; top:213px; height:17px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:1004px; top:213px; height:17px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:299px; top:398px; height:12px;"></div>
                    <div class="absolute bg-slate-300 h-[2px]" style="left:110px; top:410px; width:190px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:109px; top:410px; height:182px;"></div>
                    <div class="absolute bg-slate-300 h-[2px]" style="left:110px; top:448px; width:30px;"></div>
                    <div class="absolute bg-slate-300 h-[2px]" style="left:110px; top:520px; width:30px;"></div>
                    <div class="absolute bg-slate-300 h-[2px]" style="left:110px; top:592px; width:30px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:899px; top:398px; height:12px;"></div>
                    <div class="absolute bg-slate-300 h-[2px]" style="left:710px; top:410px; width:190px;"></div>
                    <div class="absolute bg-slate-300 w-[2px]" style="left:709px; top:410px; height:182px;"></div>
                    <div class="absolute bg-slate-300 h-[2px]" style="left:710px; top:448px; width:30px;"></div>
                    <div class="absolute bg-slate-300 h-[2px]" style="left:710px; top:520px; width:30px;"></div>
                    <div class="absolute bg-slate-300 h-[2px]" style="left:710px; top:592px; width:30px;"></div>

                    <!-- Kepala Dinas -->
                    <div class="absolute bg-[#0E2650] text-white rounded-md flex items-center justify-center text-center px-3"
                         style="left:460px; top:20px; width:280px; height:50px;">
                        <span class="text-xs font-bold tracking-wide">KEPALA DINAS</span>
                    </div>

                    <!-- Kelompok Jabatan Fungsional -->
                    <div class="absolute bg-white border-2 border-dashed border-slate-400 text-slate-700 rounded-md flex items-center justify-center text-center px-3"
                         style="left:30px; top:150px; width:320px; height:48px;">
                        <span class="text-[11px] font-bold tracking-wide">KELOMPOK JABATAN FUNGSIONAL</span>
                    </div>

                    <!-- Sekretaris -->
                    <div class="absolute bg-[#14346B] text-white rounded-md flex items-center justify-center text-center px-3"
                         style="left:740px; top:140px; width:320px; height:58px;">
                        <span class="text-sm font-bold tracking-wide">SEKRETARIS</span>
                    </div>

                    <!-- Subbagian -->
                    <div class="absolute bg-slate-100 border border-slate-300 text-slate-700 rounded-md flex flex-col items-center justify-center text-center px-2 leading-tight"
                         style="left:700px; top:230px; width:190px; height:58px;">
                        <span class="text-[9px] font-semibold text-slate-500">SUBBAG</span>
                        <span class="text-[10px] font-bold">PERENCANAAN DAN KEUANGAN</span>
                    </div>
                    <div class="absolute bg-slate-100 border border-slate-300 text-slate-700 rounded-md flex flex-col items-center justify-center text-center px-2 leading-tight"
                         style="left:910px; top:230px; width:190px; height:58px;">
                        <span class="text-[9px] font-semibold text-slate-500">SUBBAG</span>
                        <span class="text-[10px] font-bold">UMUM DAN KEPEGAWAIAN</span>
                    </div>

                    <!-- Bidang -->
                    <div class="absolute bg-[#1F4B8E] text-white rounded-md flex flex-col items-center justify-center text-center px-3 leading-tight"
                         style="left:85px; top:340px; width:430px; height:58px;">
                        <span class="text-[10px] font-semibold text-blue-100/80">BIDANG</span>
                        <span class="text-xs font-bold">REHABILITASI DAN PERLINDUNGAN JAMINAN SOSIAL</span>
                    </div>
                    <div class="absolute bg-[#1F4B8E] text-white rounded-md flex flex-col items-center justify-center text-center px-3 leading-tight"
                         style="left:685px; top:340px; width:430px; height:58px;">
                        <span class="text-[10px] font-semibold text-blue-100/80">BIDANG</span>
                        <span class="text-xs font-bold">PEMBERDAYAAN SOSIAL DAN PENANGANAN FAKIR MISKIN</span>
                    </div>

                    <!-- Seksi -->
                    <div class="absolute bg-white border border-slate-300 text-slate-700 rounded-md flex flex-col items-center justify-center text-center px-2 leading-tight"
                         style="left:140px; top:420px; width:340px; height:56px;">
                        <span class="text-[9px] font-semibold text-slate-500">SEKSI</span>
                        <span class="text-[11px] font-bold">REHABILITASI SOSIAL</span>
                    </div>
                    <div class="absolute bg-white border border-slate-300 text-slate-700 rounded-md flex flex-col items-center justify-center text-center px-2 leading-tight"
                         style="left:140px; top:492px; width:340px; height:56px;">
                        <span class="text-[9px] font-semibold text-slate-500">SEKSI</span>
                        <span class="text-[11px] font-bold">JAMINAN SOSIAL KELUARGA</span>
                    </div>
                    <div class="absolute bg-white border border-slate-300 text-slate-700 rounded-md flex flex-col items-center justify-center text-center px-2 leading-tight"
                         style="left:140px; top:564px; width:340px; height:56px;">
                        <span class="text-[9px] font-semibold text-slate-500">SEKSI</span>
                        <span class="text-[11px] font-bold">PERLINDUNGAN SOSIAL KORBAN BENCANA</span>
                    </div>
                    <div class="absolute bg-white border border-slate-300 text-slate-700 rounded-md flex flex-col items-center justify-center text-center px-2 leading-tight"
                         style="left:740px; top:420px; width:340px; height:56px;">
                        <span class="text-[9px] font-semibold text-slate-500">SEKSI</span>
                        <span class="text-[11px] font-bold">IDENTIFIKASI DAN PENGUATAN KAPASITAS</span>
                    </div>
                    <div class="absolute bg-white border border-slate-300 text-slate-700 rounded-md flex flex-col items-center justify-center text-center px-2 leading-tight"
                         style="left:740px; top:492px; width:340px; height:56px;">
                        <span class="text-[9px] font-semibold text-slate-500">SEKSI</span>
                        <span class="text-[11px] font-bold">PENDAMPINGAN BANTUAN STIMULAN DAN PENATAAN LINGKUNGAN</span>
                    </div>
                    <div class="absolute bg-white border border-slate-300 text-slate-700 rounded-md flex flex-col items-center justify-center text-center px-2 leading-tight"
                         style="left:740px; top:564px; width:340px; height:56px;">
                        <span class="text-[9px] font-semibold text-slate-500">SEKSI</span>
                        <span class="text-[11px] font-bold">PEMBERDAYAAN, MASYARAKAT, KELEMBAGAAN DAN RESTORASI SOSIAL</span>
                    </div>

                    <!-- UPT -->
                    <div class="absolute bg-slate-600 text-white rounded-md flex items-center justify-center text-center px-3"
                         style="left:500px; top:690px; width:200px; height:56px;">
                        <span class="text-sm font-bold tracking-wide">UPT</span>
                    </div>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Geser ke samping untuk melihat struktur lengkap pada layar kecil.</p>
        </div>
    </section>

    <!-- ================= KONTAK ================= -->
    <section class="bg-[#0E2650]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-white">Butuh informasi lebih lanjut?</h2>
                    <p class="text-blue-100/70 text-sm mt-2 max-w-2xl leading-relaxed">
                        Hubungi kantor Dinas Sosial Kabupaten Mukomuko pada jam pelayanan, atau sampaikan
                        kendala penyaluran bantuan melalui kanal pengaduan resmi.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                    <a href="{{ route('pengaduan') }}"
                       class="inline-flex items-center justify-center gap-2 bg-white text-[#0E2650] px-6 py-3 rounded-md text-sm font-semibold hover:bg-slate-100 transition-colors">
                        <i class="fas fa-bullhorn text-xs"></i> Ajukan Pengaduan
                    </a>
                    <a href="mailto:dinassosialkabmukomuko@gmail.com"
                       class="inline-flex items-center justify-center gap-2 border border-white/30 text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-white/10 transition-colors">
                        <i class="fas fa-envelope text-xs"></i> Kirim Surel
                    </a>
                </div>
            </div>
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
                }, { threshold: 0.15 });
                els.forEach(function (el) { io.observe(el); });
            }

            // Status buka / tutup kantor berdasarkan waktu WIB
            var badge = document.getElementById('office-status');
            function updateStatus() {
                if (!badge) return;
                var parts = new Intl.DateTimeFormat('en-US', {
                    timeZone: 'Asia/Jakarta', weekday: 'short',
                    hour: '2-digit', minute: '2-digit', hour12: false
                }).formatToParts(new Date());
                var wd = parts.find(function (p) { return p.type === 'weekday'; }).value;
                var hh = parseInt(parts.find(function (p) { return p.type === 'hour'; }).value, 10);
                var mm = parseInt(parts.find(function (p) { return p.type === 'minute'; }).value, 10);
                var mins = hh * 60 + mm;
                var map = { Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6, Sun: 0 };
                var d = map[wd];
                var open = false;
                if (d >= 1 && d <= 4) open = (mins >= 480 && mins < 960);   // Senin-Kamis 08.00-16.00
                else if (d === 5) open = (mins >= 480 && mins < 990);        // Jumat 08.00-16.30
                // Sabtu & Minggu libur
                if (open) {
                    badge.className = 'inline-flex items-center gap-2 border border-emerald-200 bg-emerald-50 text-emerald-700 text-[11px] font-semibold px-3 py-1.5 rounded-md';
                    badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> BUKA';
                } else {
                    badge.className = 'inline-flex items-center gap-2 border border-rose-200 bg-rose-50 text-[#C8102E] text-[11px] font-semibold px-3 py-1.5 rounded-md';
                    badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-[#C8102E]"></span> TUTUP';
                }
            }
            updateStatus();
            setInterval(updateStatus, 60000);
        })();
    </script>
@endsection
