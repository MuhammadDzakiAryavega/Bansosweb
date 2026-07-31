@extends('layouts.admin')

@section('title', 'Kelola PKH — Nilai Calon - Panel Portal PKH')
@section('page-title', 'Kelola PKH')
@section('page-subtitle', 'Pilih sub-kriteria yang sesuai untuk tiap kriteria. Nilai crisp akan dipakai pada perhitungan SAW.')

@section('content')

    <a href="{{ route('admin.pkh.penilaian.index') }}"
       class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[#14346B] transition-colors mb-6">
        <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
        Kembali ke daftar calon
    </a>

    <!-- Identitas calon -->
    <section class="border border-slate-200 rounded-lg bg-white overflow-hidden mb-8">
        <div class="px-6 py-6 lg:px-8 flex items-center gap-4">
            <span class="w-12 h-12 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center text-lg font-semibold flex-shrink-0">
                {{ strtoupper(substr($alternatif->user->name ?? '?', 0, 1)) }}
            </span>
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Calon Penerima</span>
                <h2 class="text-lg md:text-xl font-bold text-[#0E2650] mt-0.5">{{ $alternatif->user->name ?? '(warga terhapus)' }}</h2>
                <p class="text-sm text-slate-500 mt-0.5 tabular-nums">NIK {{ $alternatif->user->nik ?? '—' }}</p>
                @if ($alternatif->desa)
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 mt-2 rounded-md border border-slate-200 bg-slate-50 text-slate-600">
                        <i class="fas fa-location-dot text-[9px] text-slate-400"></i> Desa {{ $alternatif->desa }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    @php
        $acuanPerKriteria = $pendaftaran ? [
            'penghasilan'       => $pendaftaran->penghasilan,
            'jumlah-tanggungan' => $pendaftaran->jumlah_tanggungan . ' orang',
            'kondisi-rumah'     => $pendaftaran->kondisi_rumah,
            'status-pekerjaan'  => $pendaftaran->status_pekerjaan,
            'kepemilikan-aset'  => $pendaftaran->kepemilikan_aset,
        ] : [];
    @endphp

    <!-- ================= ACUAN DATA PENDAFTARAN ================= -->
    <section class="border border-slate-200 rounded-lg bg-white overflow-hidden mb-8">
        <div class="px-6 py-4 lg:px-8 border-b border-slate-200 flex items-center gap-2.5">
            <i class="fas fa-clipboard-list text-[#14346B] text-sm"></i>
            <h3 class="font-bold text-[#0E2650]">Acuan Data Pendaftaran</h3>
        </div>

        @if ($pendaftaran)
            <div class="px-6 py-5 lg:px-8">
                <dl class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach ($acuanPerKriteria as $slug => $nilai)
                        <div>
                            <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ $kriteria[$slug]['label'] }}</dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-1 leading-tight">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>

                <div class="mt-6 pt-5 border-t border-slate-100">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                        <i class="fas fa-camera"></i> Bukti Kondisi Rumah
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach (\App\Models\Pendaftaran::FOTO_RUMAH as $field => $flabel)
                            <div>
                                @if ($pendaftaran->{$field})
                                    <a href="{{ route('admin.pkh.pendaftaran.foto', [$pendaftaran, $field]) }}" target="_blank" rel="noopener"
                                       class="group block aspect-square rounded-md overflow-hidden border border-slate-200 bg-slate-50" title="Buka {{ $flabel }} ukuran penuh">
                                        <img src="{{ route('admin.pkh.pendaftaran.foto', [$pendaftaran, $field]) }}" alt="{{ $flabel }}" loading="lazy"
                                             class="w-full h-full object-cover group-hover:opacity-90 transition-opacity">
                                    </a>
                                @else
                                    <div class="aspect-square rounded-md border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center text-slate-300">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                                <p class="text-[11px] text-slate-500 mt-1.5 text-center leading-tight">{{ $flabel }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between gap-3 flex-wrap">
                    <span class="inline-flex items-center gap-2 text-[11px] font-semibold px-3 py-1.5 rounded-md border {{ $pendaftaran->badgeStatus() }}">
                        Pengajuan: {{ $pendaftaran->status }}
                    </span>
                    <a href="{{ route('admin.pkh.pendaftaran.show', $pendaftaran) }}"
                       class="group text-sm font-semibold text-[#14346B] inline-flex items-center gap-2 hover:underline">
                        Lihat pengajuan lengkap <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
            </div>
        @else
            <div class="px-6 py-8 lg:px-8 text-center">
                <p class="text-sm text-slate-500 leading-relaxed">
                    Warga ini ditambahkan langsung tanpa melalui form pendaftaran, sehingga belum ada data pendaftaran sebagai acuan.
                </p>
            </div>
        @endif
    </section>

    <form method="POST" action="{{ route('admin.pkh.penilaian.update', $alternatif) }}">
        @csrf
        @method('PUT')

        <div class="border border-slate-200 rounded-lg bg-white divide-y divide-slate-200 overflow-hidden">
            @foreach ($kriteria as $slug => $k)
                @php $opsi = $subPerKriteria[$slug] ?? collect(); @endphp
                <div class="px-6 py-5 lg:px-8 flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="lg:w-72 flex items-start gap-3 flex-shrink-0">
                        <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $k['icon'] }} text-sm"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900 leading-tight">
                                <span class="text-[11px] font-bold text-slate-400 mr-1">{{ $k['kode'] }}</span>{{ $k['label'] }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">Bobot {{ rtrim(rtrim(number_format($k['bobot'] * 100, 2), '0'), '.') }}% · {{ ucfirst($k['jenis']) }}</p>
                            @if (!empty($acuanPerKriteria[$slug]))
                                <p class="text-xs text-[#14346B] mt-1.5 leading-snug">
                                    <i class="fas fa-quote-left text-[9px] opacity-60 mr-1"></i>Isian warga: <span class="font-semibold">{{ $acuanPerKriteria[$slug] }}</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        @if ($opsi->isEmpty())
                            <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-4 py-3 leading-relaxed">
                                <i class="fas fa-triangle-exclamation mr-1.5"></i>
                                Belum ada sub-kriteria. Mintakan lebih dulu kepada
                                <span class="font-semibold">administrator</span> melalui menu Kelola Kriteria
                                sebelum kriteria ini dapat dinilai.
                            </p>
                        @else
                            <label for="sub-{{ $slug }}" class="sr-only">Sub-kriteria {{ $k['label'] }}</label>
                            <select name="sub[{{ $slug }}]" id="sub-{{ $slug }}"
                                    class="w-full px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                                <option value="">— Belum dinilai —</option>
                                @foreach ($opsi as $sub)
                                    <option value="{{ $sub->id }}" @selected(($pilihan[$slug] ?? null) == $sub->id)>
                                        {{ $sub->nama }} (nilai {{ $sub->nilai }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                <i class="fas fa-floppy-disk text-xs"></i> Simpan Penilaian
            </button>
            <a href="{{ route('admin.pkh.penilaian.index') }}"
               class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-6 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                Batal
            </a>
        </div>
    </form>

@endsection
