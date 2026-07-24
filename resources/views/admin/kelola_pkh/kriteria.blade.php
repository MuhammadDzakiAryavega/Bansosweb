@extends('layouts.admin')

@section('title', 'Kelola PKH — ' . $kriteria['label'] . ' - Admin Portal PKH')
@section('page-title', 'Kelola PKH')
@section('page-subtitle', 'Data master kriteria & sub-kriteria penilaian calon penerima PKH untuk metode SAW (Simple Additive Weighting).')

@section('content')

    <!-- ================= NAVIGASI ANTAR-KRITERIA ================= -->
    <div class="border border-slate-200 rounded-lg bg-white p-2 mb-8">
        <div class="flex flex-wrap gap-1.5">
            @foreach ($daftar as $slug => $item)
                @php $isAktif = $slug === $aktif; @endphp
                <a href="{{ route('admin.pkh.kriteria', $slug) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md text-sm font-semibold transition-colors {{ $isAktif ? 'bg-[#14346B] text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-[#14346B]' }}">
                    <span class="text-[10px] font-bold {{ $isAktif ? 'text-blue-200' : 'text-slate-400' }}">{{ $item['kode'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- ================= IDENTITAS KRITERIA ================= -->
    <section class="border border-slate-200 rounded-lg bg-white overflow-hidden mb-8">
        <div class="px-6 py-7 lg:px-8 lg:py-8">
            <div class="flex items-start gap-5">
                <span class="w-14 h-14 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0 text-xl">
                    <i class="fas {{ $kriteria['icon'] }}"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Kriteria {{ $kriteria['kode'] }}</span>
                    <h2 class="text-xl md:text-2xl font-bold text-[#0E2650] mt-1.5 tracking-tight">{{ $kriteria['label'] }}</h2>

                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-3 py-1.5 rounded-md border bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20">
                            <i class="fas fa-percent text-[10px]"></i> Bobot {{ rtrim(rtrim(number_format($kriteria['bobot'] * 100, 2), '0'), '.') }}%
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-3 py-1.5 rounded-md border bg-emerald-50 text-emerald-700 border-emerald-200">
                            <i class="fas fa-arrow-trend-up text-[10px]"></i> {{ ucfirst($kriteria['jenis']) }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm md:text-[15px] text-slate-600 leading-relaxed max-w-2xl">
                        {{ $kriteria['deskripsi'] }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= SUB-KRITERIA (HIMPUNAN) ================= -->
    <section>
        <div class="mb-6">
            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Data Master</span>
            <h3 class="text-lg md:text-xl font-bold text-[#0E2650] mt-2">Sub-Kriteria &amp; Nilai</h3>
            <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
            <p class="text-sm text-slate-500 mt-4 leading-relaxed max-w-2xl">
                Tentukan kategori penilaian beserta nilai crisp-nya (mis. semakin layak/miskin, nilainya semakin besar).
                Nilai inilah yang dipakai saat menilai calon penerima dan dihitung pada metode SAW.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-md border border-rose-200 bg-rose-50 text-rose-700 px-5 py-4 text-sm">
                <i class="fas fa-circle-exclamation mt-0.5"></i>
                <ul class="font-medium leading-relaxed list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">

            <!-- Tambah sub-kriteria -->
            <form method="POST" action="{{ route('admin.pkh.sub.store', $aktif) }}"
                  class="flex flex-col sm:flex-row gap-3 px-5 py-5 bg-slate-50 border-b border-slate-200">
                @csrf
                <div class="flex-1">
                    <label for="nama" class="sr-only">Nama sub-kriteria</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                           placeholder="Nama sub-kriteria, mis. < Rp1.000.000"
                           class="w-full px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                </div>
                <div class="sm:w-40">
                    <label for="nilai" class="sr-only">Nilai</label>
                    <input type="number" id="nilai" name="nilai" value="{{ old('nilai') }}" min="1" max="100" required
                           placeholder="Nilai (1-100)"
                           class="w-full px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                </div>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors flex-shrink-0">
                    <i class="fas fa-plus text-xs"></i> Tambah
                </button>
            </form>

            <!-- Daftar sub-kriteria (tiap baris dapat langsung diubah) -->
            @forelse ($subKriteria as $sub)
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4 border-b border-slate-100 last:border-b-0 hover:bg-slate-50/60 transition-colors">
                    <span class="w-8 h-8 rounded-md border border-slate-200 bg-white text-slate-400 flex items-center justify-center text-xs font-semibold flex-shrink-0">
                        {{ $loop->iteration }}
                    </span>
                    <form method="POST" action="{{ route('admin.pkh.sub.update', $sub) }}"
                          class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                        @csrf
                        @method('PUT')
                        <input type="text" name="nama" value="{{ $sub->nama }}" required maxlength="120"
                               class="flex-1 px-4 py-2.5 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                        <input type="number" name="nilai" value="{{ $sub->nilai }}" min="1" max="100" required
                               class="sm:w-28 px-4 py-2.5 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-4 py-2.5 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors flex-shrink-0"
                                title="Simpan perubahan">
                            <i class="fas fa-floppy-disk text-xs"></i> Simpan
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.pkh.sub.destroy', $sub) }}"
                          onsubmit="return confirm('Hapus sub-kriteria &quot;{{ $sub->nama }}&quot;?');"
                          class="flex-shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-10 h-10 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-[#C8102E] hover:border-[#C8102E] hover:text-white transition-colors"
                                title="Hapus">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="px-6 py-14 text-center">
                    <span class="w-12 h-12 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-xl mb-4">
                        <i class="fas fa-layer-group"></i>
                    </span>
                    <p class="font-semibold text-slate-800">Belum ada sub-kriteria</p>
                    <p class="text-sm text-slate-500 mt-1">Tambahkan kategori penilaian melalui formulir di atas.</p>
                </div>
            @endforelse
        </div>
    </section>

@endsection
