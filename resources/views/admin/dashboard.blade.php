@extends('layouts.admin')

{{-- Satu tampilan untuk dua peran. Seluruh isi (kartu, panel, pintasan) disusun
     di AuthController sesuai kewenangan, sehingga tidak ada tautan ke modul
     yang justru akan ditolak 403. --}}

@section('title', 'Dashboard - Panel Portal PKH')
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
                    {{ $pengantar }}
                </p>

                <div class="mt-7 flex flex-col sm:flex-row gap-3">
                    @foreach ($tombolUtama as $tombol)
                        <a href="{{ $tombol['url'] }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-md text-sm font-semibold transition-colors {{ $tombol['utama']
                               ? 'bg-[#14346B] text-white hover:bg-[#0E2650]'
                               : 'border border-slate-300 text-slate-700 hover:border-[#14346B] hover:text-[#14346B]' }}">
                            <i class="fas {{ $tombol['icon'] }} text-xs"></i> {{ $tombol['label'] }}
                        </a>
                    @endforeach
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
                        <dd class="font-semibold text-slate-800">{{ Auth::user()->labelRole() }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-slate-600">Wilayah</dt>
                        <dd class="font-semibold text-slate-800">Kec. Teramang Jaya</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-slate-600">{{ $sorotan['label'] }}</dt>
                        <dd class="font-semibold {{ $sorotan['accent'] ? 'text-[#C8102E]' : 'text-slate-800' }}">
                            {{ $sorotan['nilai'] }}
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

        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-slate-200 border border-slate-200 rounded-lg overflow-hidden">
            @foreach($kartuStatistik as $kartu)
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

    <!-- ================= DAFTAR TERBARU & AKSES CEPAT ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

        <!-- Daftar terbaru sesuai peran -->
        <section class="lg:col-span-8">
            <div class="flex items-end justify-between gap-4 mb-6">
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $panelTerbaru['kicker'] }}</span>
                    <h2 class="text-xl md:text-2xl font-bold text-[#0E2650] mt-2">{{ $panelTerbaru['judul'] }}</h2>
                    <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                </div>
                <a href="{{ $panelTerbaru['lihatSemua'] }}"
                   class="group text-sm font-semibold text-[#14346B] inline-flex items-center gap-2 flex-shrink-0 pb-1">
                    Lihat semua
                    <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>

            <div class="border border-slate-200 rounded-lg bg-white divide-y divide-slate-200 overflow-hidden">
                @forelse ($panelTerbaru['items'] as $item)
                    <a href="{{ $item['url'] }}"
                       class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition-colors">
                        <span class="w-9 h-9 flex-shrink-0 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center text-sm font-semibold">
                            {{ $item['inisial'] }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900 truncate">{{ $item['judul'] }}</p>
                            <p class="text-xs text-slate-500 truncate mt-0.5">{{ $item['subjudul'] }}</p>
                        </div>
                        <span class="flex-shrink-0 text-[11px] font-semibold px-3 py-1.5 rounded-md border {{ $item['badgeKelas'] }}">
                            {{ $item['badge'] }}
                        </span>
                    </a>
                @empty
                    <div class="px-6 py-16 text-center">
                        <span class="w-12 h-12 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-xl mb-4">
                            <i class="fas {{ $panelTerbaru['kosongIcon'] }}"></i>
                        </span>
                        <p class="font-semibold text-slate-800">{{ $panelTerbaru['kosongJudul'] }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ $panelTerbaru['kosongPesan'] }}</p>
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

            <div class="border border-slate-200 rounded-lg bg-white divide-y divide-slate-200 overflow-hidden">
                @foreach($aksesCepat as $item)
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
