@extends('layouts.admin')

@section('title', 'Kelola PKH — Penilaian Calon - Panel Portal PKH')
@section('page-title', 'Kelola PKH')
@section('page-subtitle', 'Daftar calon penerima (warga terdaftar) beserta penilaian tiap kriteria sebagai dasar perhitungan SAW.')

@section('content')

    @php $jumlahKriteria = count($kriteria); @endphp

    <!-- ================= DAFTAR CALON ================= -->
    <div class="flex items-end justify-between gap-4 mb-6">
        <div>
            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Alternatif</span>
            <h3 class="text-lg md:text-xl font-bold text-[#0E2650] mt-2">Daftar Calon Penerima</h3>
            <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
        </div>
        <a href="{{ route('admin.pkh.hasil') }}"
           class="group text-sm font-semibold text-[#14346B] inline-flex items-center gap-2 flex-shrink-0 pb-1">
            Lihat hasil akhir
            <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
        </a>
    </div>

    <form method="GET" action="{{ route('admin.pkh.penilaian.index') }}" class="mb-5 flex flex-wrap items-center gap-3">
        <label for="desaFilter" class="text-sm font-semibold text-slate-600">Desa:</label>
        <select name="desa" id="desaFilter" onchange="this.form.submit()"
                class="px-4 py-2.5 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
            <option value="">Semua Desa</option>
            @foreach ($desaList as $desaOpsi)
                <option value="{{ $desaOpsi }}" @selected($desaAktif === $desaOpsi)>{{ $desaOpsi }}</option>
            @endforeach
        </select>
        @if ($desaAktif)
            <a href="{{ route('admin.pkh.penilaian.index') }}" class="text-sm font-semibold text-slate-500 hover:text-[#14346B] transition-colors">Reset</a>
        @endif
    </form>

    <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Calon Penerima</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">NIK</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Desa</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kelengkapan</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="text-right px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($alternatif as $alt)
                        @php
                            $terisi = $alt->penilaian->count();
                            $lengkap = $terisi >= $jumlahKriteria;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 flex-shrink-0 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center text-sm font-semibold">
                                        {{ strtoupper(substr($alt->user->name ?? '?', 0, 1)) }}
                                    </span>
                                    <span class="font-semibold text-slate-900">{{ $alt->user->name ?? '(warga terhapus)' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 tabular-nums whitespace-nowrap">{{ $alt->user->nik ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-md border border-slate-200 bg-slate-50 text-slate-600">
                                    <i class="fas fa-location-dot text-[9px] text-slate-400"></i> {{ $alt->desa ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5 min-w-[120px]">
                                    <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $lengkap ? 'bg-emerald-500' : 'bg-[#14346B]' }}"
                                             style="width: {{ $jumlahKriteria ? round($terisi / $jumlahKriteria * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-500 tabular-nums">{{ $terisi }}/{{ $jumlahKriteria }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border whitespace-nowrap {{ $lengkap ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    {{ $lengkap ? 'Lengkap' : 'Belum lengkap' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.pkh.penilaian.edit', $alt) }}"
                                       class="inline-flex items-center gap-2 border border-slate-300 text-slate-700 px-4 py-2 rounded-md text-xs font-semibold hover:bg-[#14346B] hover:border-[#14346B] hover:text-white transition-colors">
                                        <i class="fas fa-clipboard-check text-xs"></i> Nilai
                                    </a>
                                    <form method="POST" action="{{ route('admin.pkh.penilaian.destroy', $alt) }}"
                                          onsubmit="return confirm('Keluarkan {{ $alt->user->name ?? 'calon ini' }} dari daftar calon? Penilaiannya ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-9 h-9 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-[#C8102E] hover:border-[#C8102E] hover:text-white transition-colors"
                                                title="Keluarkan dari daftar">
                                            <i class="fas fa-user-minus text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <span class="w-12 h-12 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-xl mb-4">
                                    <i class="fas fa-users"></i>
                                </span>
                                <p class="font-semibold text-slate-800">Belum ada calon penerima</p>
                                <p class="text-sm text-slate-500 mt-1">Calon penerima muncul otomatis setelah pengajuan warga diverifikasi pada menu Pendaftaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
