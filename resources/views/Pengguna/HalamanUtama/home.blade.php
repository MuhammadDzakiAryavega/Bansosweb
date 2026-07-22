@extends('layouts.layout')
@section('title', 'Beranda - Portal PKH Dinas Sosial Kabupaten Mukomuko')
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

    <!-- ================= HERO ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">

                <!-- Kolom kiri: identitas & penjelasan -->
                <div class="lg:col-span-7">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-block w-7 border border-slate-300 overflow-hidden">
                            <span class="block h-[7px] bg-[#C8102E]"></span>
                            <span class="block h-[7px] bg-white"></span>
                        </span>
                        <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                            Portal Resmi Program Keluarga Harapan
                        </span>
                    </div>

                    <h1 class="text-3xl md:text-4xl lg:text-[2.6rem] font-bold text-[#0E2650] leading-[1.2] tracking-tight">
                        Sistem Pendukung Keputusan Penetapan Penerima
                        <span class="block">Program Keluarga Harapan</span>
                    </h1>

                    <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>

                    <p class="mt-6 text-base md:text-[1.05rem] text-slate-600 leading-relaxed max-w-2xl">
                        Layanan informasi Program Keluarga Harapan (PKH) Kecamatan Teramang Jaya, Kabupaten Mukomuko.
                        Penetapan calon penerima dilakukan secara objektif menggunakan metode
                        <span class="font-semibold text-slate-800">Simple Additive Weighting (SAW)</span>
                        agar bantuan tersalurkan tepat sasaran.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="#cek-nik"
                           class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                            <i class="fas fa-magnifying-glass text-xs"></i> Cek Status Kepesertaan
                        </a>
                        <a href="{{ route('pengaduan') }}"
                           class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-6 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                            <i class="fas fa-bullhorn text-xs"></i> Ajukan Pengaduan
                        </a>
                    </div>

                    <dl class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-px bg-slate-200 border border-slate-200 rounded-md overflow-hidden">
                        @php
                            $hero_facts = [
                                ['label' => 'Sumber Data', 'value' => 'DTKS &amp; Verifikasi Lapangan'],
                                ['label' => 'Metode Penilaian', 'value' => 'Simple Additive Weighting'],
                                ['label' => 'Wilayah Layanan', 'value' => 'Kec. Teramang Jaya'],
                            ];
                        @endphp
                        @foreach($hero_facts as $fact)
                        <div class="bg-white px-5 py-4">
                            <dt class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">{{ $fact['label'] }}</dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-1">{!! $fact['value'] !!}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                <!-- Kolom kanan: panel cek status -->
                <div id="cek-nik" class="lg:col-span-5 scroll-mt-28">
                    <div class="border border-slate-200 rounded-lg overflow-hidden bg-white">
                        <div class="bg-[#14346B] px-6 py-4">
                            <h2 class="text-white font-semibold text-base flex items-center gap-2">
                                <i class="fas fa-id-card text-sm opacity-80"></i> Cek Status Kepesertaan
                            </h2>
                            <p class="text-blue-100/80 text-xs mt-1">Masukkan Nomor Induk Kependudukan (16 digit)</p>
                        </div>

                        <form id="form-cek-nik" class="px-6 py-6" novalidate>
                            <label for="nik" class="block text-sm font-semibold text-slate-700 mb-2">NIK</label>
                            <input type="text" id="nik" name="nik" inputmode="numeric" maxlength="16"
                                   placeholder="Contoh: 1706xxxxxxxxxxxx"
                                   class="w-full border border-slate-300 rounded-md px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">

                            <button type="submit"
                                    class="mt-4 w-full bg-[#14346B] text-white py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                                Periksa Status
                            </button>

                            <p id="nik-message" class="hidden mt-4 text-sm rounded-md border px-4 py-3 leading-relaxed"></p>

                            <p class="mt-4 text-xs text-slate-500 leading-relaxed border-t border-slate-100 pt-4">
                                Data kepesertaan bersifat rahasia dan hanya digunakan untuk keperluan verifikasi
                                penyaluran bantuan sosial.
                            </p>
                        </form>
                    </div>

                    <!-- Jam layanan -->
                    <div class="mt-4 border border-slate-200 rounded-lg bg-slate-50 px-6 py-5">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Jam Pelayanan Kantor</h3>
                        <dl class="text-sm divide-y divide-slate-200">
                            <div class="flex justify-between py-1.5">
                                <dt class="text-slate-600">Senin &ndash; Kamis</dt>
                                <dd class="font-semibold text-slate-800">08.00 &ndash; 16.00 WIB</dd>
                            </div>
                            <div class="flex justify-between py-1.5">
                                <dt class="text-slate-600">Jumat</dt>
                                <dd class="font-semibold text-slate-800">08.00 &ndash; 16.30 WIB</dd>
                            </div>
                            <div class="flex justify-between py-1.5">
                                <dt class="text-slate-600">Sabtu &ndash; Minggu</dt>
                                <dd class="font-semibold text-[#C8102E]">Libur</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= LAYANAN ================= -->
    <section class="bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
            <div class="mb-8">
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Layanan</span>
                <h2 class="text-2xl md:text-3xl font-bold text-[#0E2650] mt-2">Layanan Portal PKH</h2>
                <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
            </div>

            @php
                $layanan = [
                    [
                        'icon'  => 'fa-magnifying-glass',
                        'title' => 'Cek Status Kepesertaan',
                        'desc'  => 'Periksa status kepesertaan PKH berdasarkan Nomor Induk Kependudukan.',
                        'link'  => '#cek-nik',
                        'label' => 'Cek Sekarang',
                    ],
                    [
                        'icon'  => 'fa-circle-info',
                        'title' => 'Informasi Program',
                        'desc'  => 'Syarat, komponen bantuan, dan kriteria kelayakan penerima PKH.',
                        'link'  => route('tentang'),
                        'label' => 'Baca Informasi',
                    ],
                    [
                        'icon'  => 'fa-bullhorn',
                        'title' => 'Pengaduan Masyarakat',
                        'desc'  => 'Sampaikan kendala penyaluran bantuan untuk ditindaklanjuti petugas.',
                        'link'  => route('pengaduan'),
                        'label' => 'Ajukan Pengaduan',
                    ],
                    [
                        'icon'  => 'fa-clock-rotate-left',
                        'title' => 'Riwayat Pengaduan',
                        'desc'  => 'Pantau perkembangan dan status tindak lanjut pengaduan yang diajukan.',
                        'link'  => route('pengaduan.riwayat'),
                        'label' => 'Lihat Riwayat',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($layanan as $item)
                <a href="{{ $item['link'] }}"
                   class="reveal group bg-white border border-slate-200 rounded-lg p-6 hover:border-[#14346B] transition-colors flex flex-col"
                   style="transition-delay: {{ $loop->index * 60 }}ms">
                    <span class="w-10 h-10 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center mb-5 group-hover:bg-[#14346B] group-hover:text-white transition-colors">
                        <i class="fas {{ $item['icon'] }} text-sm"></i>
                    </span>
                    <h3 class="font-semibold text-slate-900 mb-2">{{ $item['title'] }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed flex-grow">{{ $item['desc'] }}</p>
                    <span class="mt-5 text-sm font-semibold text-[#14346B] inline-flex items-center gap-2">
                        {{ $item['label'] }}
                        <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ================= TAHAPAN SELEKSI (SAW) ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">

                <div class="lg:col-span-4">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Mekanisme</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-[#0E2650] mt-2 leading-snug">
                        Tahapan Penetapan Penerima PKH
                    </h2>
                    <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                    <p class="mt-6 text-slate-600 leading-relaxed text-sm md:text-base">
                        Penilaian kelayakan calon penerima dilakukan melalui empat tahapan baku menggunakan metode
                        <strong class="text-slate-800">Simple Additive Weighting (SAW)</strong>. Seluruh kriteria dinilai
                        dengan ukuran yang sama sehingga hasil keputusan dapat ditelusuri dan dipertanggungjawabkan.
                    </p>
                    <p class="mt-4 text-sm text-slate-500 leading-relaxed">
                        Kriteria penilaian meliputi pendapatan keluarga, jumlah tanggungan, kondisi hunian, serta
                        kondisi khusus anggota keluarga. Bobot setiap kriteria ditetapkan oleh Dinas Sosial
                        Kabupaten Mukomuko.
                    </p>
                </div>

                @php
                    $saw_steps = [
                        [
                            'title' => 'Pengumpulan Data',
                            'desc'  => 'Data Terpadu Kesejahteraan Sosial (DTKS) diintegrasikan dengan hasil verifikasi faktual di lapangan untuk memastikan kondisi ekonomi calon penerima sesuai keadaan sebenarnya.',
                        ],
                        [
                            'title' => 'Normalisasi Kriteria',
                            'desc'  => 'Setiap indikator kelayakan seperti penghasilan dan kelayakan rumah dikonversi ke dalam skala nilai yang seragam agar seluruh data dapat dibandingkan secara setara.',
                        ],
                        [
                            'title' => 'Pembobotan Prioritas',
                            'desc'  => 'Nilai setiap kriteria dikalikan dengan bobot kepentingannya. Kriteria yang lebih mendesak, misalnya keberadaan lansia atau penyandang disabilitas, memperoleh prioritas lebih tinggi.',
                        ],
                        [
                            'title' => 'Perankingan Hasil',
                            'desc'  => 'Sistem menyusun urutan calon penerima berdasarkan nilai akhir. Hasil perankingan menjadi dasar rekomendasi penetapan penerima bantuan yang terbuka untuk ditelusuri.',
                        ],
                    ];
                @endphp

                <div class="lg:col-span-8">
                    <ol class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-slate-200 border border-slate-200 rounded-lg overflow-hidden">
                        @foreach($saw_steps as $index => $step)
                        <li class="reveal bg-white p-6 lg:p-7" style="transition-delay: {{ $index * 80 }}ms">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="w-9 h-9 flex items-center justify-center rounded-md border border-[#14346B]/20 text-[#14346B] font-bold text-sm">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <h3 class="font-semibold text-slate-900">{{ $step['title'] }}</h3>
                            </div>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= AJAKAN PENGADUAN ================= -->
    <section class="bg-[#0E2650]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-white">Menemukan kendala penyaluran bantuan?</h2>
                    <p class="text-blue-100/70 text-sm mt-2 max-w-2xl leading-relaxed">
                        Sampaikan laporan Anda melalui kanal pengaduan resmi. Setiap laporan dicatat dan
                        ditindaklanjuti oleh petugas Dinas Sosial Kabupaten Mukomuko.
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

            // Validasi format NIK (pemeriksaan mandiri belum terhubung ke data kepesertaan)
            var form = document.getElementById('form-cek-nik');
            var input = document.getElementById('nik');
            var message = document.getElementById('nik-message');
            if (!form || !input || !message) return;

            input.addEventListener('input', function () {
                input.value = input.value.replace(/\D/g, '').slice(0, 16);
            });

            function showMessage(text, type) {
                message.textContent = text;
                message.className = type === 'error'
                    ? 'mt-4 text-sm rounded-md border px-4 py-3 leading-relaxed bg-rose-50 border-rose-200 text-rose-700'
                    : 'mt-4 text-sm rounded-md border px-4 py-3 leading-relaxed bg-slate-50 border-slate-200 text-slate-600';
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var nik = input.value.trim();
                if (nik.length !== 16) {
                    showMessage('NIK harus terdiri dari 16 digit angka.', 'error');
                    return;
                }
                showMessage('Pemeriksaan status secara mandiri belum tersedia. Silakan ajukan pertanyaan melalui menu Pengaduan atau datang ke kantor Dinas Sosial pada jam pelayanan.', 'info');
            });
        })();
    </script>
@endsection
