@extends('layouts.admin')

@section('title', 'Dashboard - Admin Portal PKH')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan data Portal PKH Kecamatan Teramang Jaya, Kabupaten Mukomuko.')

@section('content')

    <!-- ================= PANEL SAMBUTAN & WAKTU ================= -->
    <section class="border border-slate-200 rounded-lg bg-white overflow-hidden mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-12">

            <!-- Sambutan -->
            <div class="lg:col-span-8 px-6 py-7 lg:px-8 lg:py-8">
                <div class="flex items-center gap-3 mb-5">
                    <span class="inline-block w-7 border border-slate-300 overflow-hidden">
                        <span class="block h-[7px] bg-[#C8102E]"></span>
                        <span class="block h-[7px] bg-white"></span>
                    </span>
                    <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
                        Area Terbatas &middot; Petugas Dinas Sosial
                    </span>
                </div>

                <h2 class="text-xl md:text-2xl font-bold text-[#0E2650] leading-snug tracking-tight">
                    <span id="greeting">Selamat Datang</span>, {{ Auth::user()->name }}
                </h2>

                <div class="w-12 h-1 bg-[#C8102E] mt-5"></div>

                <p class="mt-5 text-sm md:text-[15px] text-slate-600 leading-relaxed max-w-2xl">
                    Pantau jumlah pengguna terdaftar serta tindak lanjuti pengaduan masyarakat yang masuk melalui
                    Portal PKH. Setiap perubahan status pengaduan tercatat dan dapat ditelusuri.
                </p>

                <div class="mt-7 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('admin.pengaduan.index') }}"
                       class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                        <i class="fas fa-bullhorn text-xs"></i> Kelola Pengaduan
                    </a>
                    <a href="{{ route('admin.user.index') }}"
                       class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-6 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                        <i class="fas fa-users text-xs"></i> Kelola Pengguna
                    </a>
                </div>
            </div>

            <!-- Waktu server -->
            <div class="lg:col-span-4 bg-slate-50 border-t lg:border-t-0 lg:border-l border-slate-200 px-6 py-7 lg:px-8 lg:py-8">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Waktu Server (WIB)</h3>
                <p id="clock" class="text-3xl font-bold text-[#0E2650] tabular-nums tracking-tight">--:--:--</p>
                <p id="date" class="text-sm text-slate-600 mt-1">&nbsp;</p>

                <dl class="mt-6 text-sm divide-y divide-slate-200 border-t border-slate-200">
                    <div class="flex justify-between py-2">
                        <dt class="text-slate-600">Peran</dt>
                        <dd class="font-semibold text-slate-800">Administrator</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-slate-600">Wilayah</dt>
                        <dd class="font-semibold text-slate-800">Kec. Teramang Jaya</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-slate-600">Perlu ditindak</dt>
                        <dd class="font-semibold {{ $statistik['perlu_ditindak'] > 0 ? 'text-[#C8102E]' : 'text-slate-800' }}">
                            {{ $statistik['perlu_ditindak'] }} pengaduan
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <!-- ================= RINGKASAN DATA ================= -->
    <section class="mb-10">
        <div class="mb-6">
            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Statistik</span>
            <h2 class="text-xl md:text-2xl font-bold text-[#0E2650] mt-2">Ringkasan Data Portal</h2>
            <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
        </div>

        @php
            $kartu_statistik = [
                [
                    'label' => 'Total Pengguna',
                    'value' => $statistik['pengguna'],
                    'desc'  => 'Akun masyarakat terdaftar',
                    'icon'  => 'fa-users',
                ],
                [
                    'label' => 'Total Pengaduan',
                    'value' => $statistik['pengaduan'],
                    'desc'  => 'Laporan masuk seluruhnya',
                    'icon'  => 'fa-bullhorn',
                ],
                [
                    'label' => 'Perlu Ditindak',
                    'value' => $statistik['perlu_ditindak'],
                    'desc'  => 'Baru, pending & dalam proses',
                    'icon'  => 'fa-triangle-exclamation',
                    'accent' => true,
                ],
                [
                    'label' => 'Pengaduan Selesai',
                    'value' => $statistik['selesai'],
                    'desc'  => 'Telah ditindaklanjuti tuntas',
                    'icon'  => 'fa-circle-check',
                ],
            ];
        @endphp

        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-slate-200 border border-slate-200 rounded-lg overflow-hidden">
            @foreach($kartu_statistik as $kartu)
                <div class="reveal bg-white px-6 py-6" style="transition-delay: {{ $loop->index * 60 }}ms">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">{{ $kartu['label'] }}</dt>
                        <span class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0 {{ !empty($kartu['accent']) ? 'bg-[#C8102E]/5 text-[#C8102E]' : 'bg-[#14346B]/5 text-[#14346B]' }}">
                            <i class="fas {{ $kartu['icon'] }} text-xs"></i>
                        </span>
                    </div>
                    <dd class="text-3xl font-bold tabular-nums mt-3 {{ !empty($kartu['accent']) && $kartu['value'] > 0 ? 'text-[#C8102E]' : 'text-[#0E2650]' }}">
                        {{ $kartu['value'] }}
                    </dd>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $kartu['desc'] }}</p>
                </div>
            @endforeach
        </dl>
    </section>

    <!-- ================= PENGADUAN TERBARU & AKSES CEPAT ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

        <!-- Pengaduan terbaru -->
        <section class="lg:col-span-8">
            <div class="flex items-end justify-between gap-4 mb-6">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Tindak Lanjut</span>
                    <h2 class="text-xl md:text-2xl font-bold text-[#0E2650] mt-2">Pengaduan Terbaru</h2>
                    <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                </div>
                <a href="{{ route('admin.pengaduan.index') }}"
                   class="group text-sm font-semibold text-[#14346B] inline-flex items-center gap-2 flex-shrink-0 pb-1">
                    Lihat semua
                    <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>

            <div class="border border-slate-200 rounded-lg bg-white divide-y divide-slate-200 overflow-hidden">
                @forelse ($pengaduanTerbaru as $item)
                    @php
                        $badge = match ($item->status_pengaduan) {
                            'Baru'         => 'bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20',
                            'Pending'      => 'bg-amber-50 text-amber-700 border-amber-200',
                            'Dalam Proses' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'Selesai'      => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Decline'      => 'bg-rose-50 text-[#C8102E] border-rose-200',
                            default        => 'bg-slate-50 text-slate-600 border-slate-200',
                        };
                    @endphp
                    <a href="{{ route('admin.pengaduan.show', $item->id_pengaduan) }}"
                       class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition-colors">
                        <span class="w-9 h-9 flex-shrink-0 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr($item->nama_pengadu, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900 truncate">{{ $item->judul_pengaduan }}</p>
                            <p class="text-xs text-slate-500 truncate mt-0.5">
                                {{ $item->nama_pengadu }} &middot; {{ $item->tanggal_pengaduan->translatedFormat('d M Y, H:i') }} WIB
                            </p>
                        </div>
                        <span class="flex-shrink-0 text-[11px] font-semibold px-3 py-1.5 rounded-md border {{ $badge }}">
                            {{ $item->status_pengaduan }}
                        </span>
                    </a>
                @empty
                    <div class="px-6 py-16 text-center">
                        <span class="w-12 h-12 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-xl mb-4">
                            <i class="fas fa-inbox"></i>
                        </span>
                        <p class="font-semibold text-slate-800">Belum ada pengaduan masuk</p>
                        <p class="text-sm text-slate-500 mt-1">Pengaduan dari masyarakat akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Akses cepat -->
        <aside class="lg:col-span-4">
            <div class="mb-6">
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Pintasan</span>
                <h2 class="text-xl md:text-2xl font-bold text-[#0E2650] mt-2">Akses Cepat</h2>
                <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
            </div>

            @php
                $akses_cepat = [
                    [
                        'icon'  => 'fa-bullhorn',
                        'title' => 'Kelola Pengaduan',
                        'desc'  => 'Tinjau laporan &amp; ubah status tindak lanjut.',
                        'link'  => route('admin.pengaduan.index'),
                    ],
                    [
                        'icon'  => 'fa-newspaper',
                        'title' => 'Kelola Berita',
                        'desc'  => 'Tulis &amp; publikasikan informasi program.',
                        'link'  => route('admin.berita.index'),
                    ],
                    [
                        'icon'  => 'fa-images',
                        'title' => 'Kelola Galeri',
                        'desc'  => 'Unggah &amp; tata dokumentasi kegiatan.',
                        'link'  => route('admin.galeri.index'),
                    ],
                    [
                        'icon'  => 'fa-users',
                        'title' => 'Kelola Pengguna',
                        'desc'  => 'Atur data akun masyarakat &amp; petugas.',
                        'link'  => route('admin.user.index'),
                    ],
                    [
                        'icon'  => 'fa-folder-open',
                        'title' => 'Kelola Arsip',
                        'desc'  => 'Catat surat masuk, keluar &amp; dokumen penting.',
                        'link'  => route('admin.arsip.index'),
                    ],
                ];
            @endphp

            <div class="border border-slate-200 rounded-lg bg-white divide-y divide-slate-200 overflow-hidden">
                @foreach($akses_cepat as $item)
                    <a href="{{ $item['link'] }}" class="group flex items-start gap-4 px-5 py-5 hover:bg-slate-50 transition-colors">
                        <span class="w-10 h-10 flex-shrink-0 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center group-hover:bg-[#14346B] group-hover:text-white transition-colors">
                            <i class="fas {{ $item['icon'] }} text-sm"></i>
                        </span>
                        <span class="min-w-0">
                            <span class="block font-semibold text-slate-900">{{ $item['title'] }}</span>
                            <span class="block text-xs text-slate-500 leading-relaxed mt-1">{!! $item['desc'] !!}</span>
                        </span>
                        <i class="fas fa-arrow-right text-[10px] text-slate-300 mt-3.5 ml-auto flex-shrink-0 transition-transform group-hover:translate-x-1 group-hover:text-[#14346B]"></i>
                    </a>
                @endforeach
            </div>

            <div class="mt-4 border border-slate-200 rounded-lg bg-slate-50 px-5 py-5">
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
        </aside>
    </div>

@endsection
