@extends('layouts.admin')

@section('title', 'Kelola PKH — Hasil Akhir - Panel Portal PKH')
@section('page-title', 'Kelola PKH')
@section('page-subtitle', 'Hasil akhir perankingan calon penerima PKH berdasarkan metode SAW (Simple Additive Weighting).')

@section('content')

    <!-- ================= IDENTITAS HALAMAN ================= -->
    <section class="border border-slate-200 rounded-lg bg-white overflow-hidden mb-8">
        <div class="px-6 py-7 lg:px-8 lg:py-8">
            <div class="flex items-start gap-5">
                <span class="w-14 h-14 rounded-md bg-[#C8102E]/5 text-[#C8102E] flex items-center justify-center flex-shrink-0 text-xl">
                    <i class="fas fa-trophy"></i>
                </span>
                <div class="min-w-0">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Perankingan SAW</span>
                    <h2 class="text-xl md:text-2xl font-bold text-[#0E2650] mt-1.5 tracking-tight">Hasil Akhir</h2>
                    <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                    <p class="mt-4 text-sm md:text-[15px] text-slate-600 leading-relaxed max-w-2xl">
                        Peringkat disusun dari skor preferensi <span class="font-semibold">V<sub>i</sub> = Σ (bobot × nilai ternormalisasi)</span>.
                        Semakin tinggi skor, semakin diprioritaskan sebagai penerima PKH.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bobot kriteria -->
        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 lg:px-8">
            <div class="flex flex-wrap gap-2">
                @foreach ($kriteria as $k)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-slate-200 bg-white text-xs font-semibold text-slate-600">
                        <span class="text-[10px] font-bold text-[#14346B]">{{ $k['kode'] }}</span>
                        {{ $k['label'] }}
                        <span class="text-slate-400">·</span>
                        <span class="text-[#14346B]">{{ rtrim(rtrim(number_format($k['bobot'] * 100, 2), '0'), '.') }}%</span>
                        <span class="text-[10px] text-emerald-600">{{ ucfirst($k['jenis']) }}</span>
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    <form method="GET" action="{{ route('admin.pkh.hasil') }}" class="mb-8 flex flex-wrap items-center gap-3 border border-slate-200 rounded-lg bg-white px-5 py-4">
        <label for="desaFilter" class="text-sm font-semibold text-slate-700 flex items-center gap-2">
            <i class="fas fa-location-dot text-[#14346B] text-xs"></i> Desa
        </label>
        <select name="desa" id="desaFilter" onchange="this.form.submit()"
                class="px-4 py-2.5 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
            <option value="">Semua Desa (gabungan)</option>
            @foreach ($desaList as $desaOpsi)
                <option value="{{ $desaOpsi }}" @selected($desaAktif === $desaOpsi)>{{ $desaOpsi }}</option>
            @endforeach
        </select>
        @if ($desaAktif)
            <span class="text-xs text-slate-500 leading-relaxed">
                Normalisasi &amp; perankingan dihitung khusus calon <span class="font-semibold text-slate-700">Desa {{ $desaAktif }}</span>.
            </span>
            <a href="{{ route('admin.pkh.hasil') }}" class="text-sm font-semibold text-slate-500 hover:text-[#14346B] transition-colors ml-auto">Reset</a>
        @else
            <span class="text-xs text-slate-500">Seluruh calon se-Teramang Jaya. Pilih desa untuk perankingan per desa.</span>
        @endif
    </form>

    @if ($belum->isNotEmpty())
        <div class="mb-8 flex items-start gap-3 rounded-md border border-amber-200 bg-amber-50 text-amber-800 px-5 py-4 text-sm">
            <i class="fas fa-triangle-exclamation mt-0.5"></i>
            <div class="leading-relaxed">
                <span class="font-semibold">{{ $belum->count() }} calon</span> belum dinilai lengkap sehingga tidak diikutkan dalam perankingan:
                <span class="font-medium">
                    {{ $belum->map(fn ($r) => ($r['alt']->user->name ?? 'Tanpa nama') . " ({$r['terisi']}/" . count($kriteria) . ')')->join(', ') }}.
                </span>
                Lengkapi melalui <a href="{{ route('admin.pkh.penilaian.index') }}" class="font-semibold underline hover:no-underline">Penilaian Calon</a>.
            </div>
        </div>
    @endif

    <!-- ================= PERANKINGAN ================= -->
    <div class="mb-4">
        <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Keluaran</span>
        <h3 class="text-lg md:text-xl font-bold text-[#0E2650] mt-2">Peringkat Calon Penerima</h3>
        <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
    </div>

    @if ($hasil->isEmpty())
        <div class="border border-slate-200 rounded-lg bg-white px-6 py-16 text-center">
            <span class="w-14 h-14 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-2xl mb-5">
                <i class="fas fa-list-ol"></i>
            </span>
            <p class="font-semibold text-slate-800">Belum ada hasil perankingan</p>
            <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">
                Perankingan muncul setelah minimal satu calon penerima dinilai lengkap pada seluruh kriteria.
            </p>
            <a href="{{ route('admin.pkh.penilaian.index') }}"
               class="inline-flex items-center gap-2 mt-6 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                <i class="fas fa-clipboard-check text-xs"></i> Nilai Calon Penerima
            </a>
        </div>
    @else
        <!-- Tabel normalisasi & skor -->
        <div class="border border-slate-200 rounded-lg bg-white overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Peringkat</th>
                            <th class="text-left px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Calon Penerima</th>
                            @unless ($desaAktif)
                                <th class="text-left px-4 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Desa</th>
                            @endunless
                            @foreach ($kriteria as $k)
                                <th class="text-center px-4 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500" title="{{ $k['label'] }} (ternormalisasi)">{{ $k['kode'] }}</th>
                            @endforeach
                            <th class="text-right px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Skor (V)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($hasil as $row)
                            @php $peringkat = $loop->iteration; @endphp
                            <tr class="{{ $peringkat === 1 ? 'bg-emerald-50/40' : 'hover:bg-slate-50' }} transition-colors">
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md text-sm font-bold {{ $peringkat === 1 ? 'bg-[#C8102E] text-white' : ($peringkat <= 3 ? 'bg-[#14346B]/10 text-[#14346B]' : 'bg-slate-100 text-slate-500') }}">
                                        {{ $peringkat }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-900 truncate">{{ $row['alt']->user->name ?? '(warga terhapus)' }}</p>
                                            <p class="text-xs text-slate-400 tabular-nums">NIK {{ $row['alt']->user->nik ?? '—' }}</p>
                                        </div>
                                        @if ($peringkat === 1)
                                            <span class="text-[10px] font-semibold px-2 py-1 rounded border bg-[#C8102E]/5 text-[#C8102E] border-[#C8102E]/20 whitespace-nowrap">Prioritas</span>
                                        @endif
                                    </div>
                                </td>
                                @unless ($desaAktif)
                                    <td class="px-4 py-4 text-sm text-slate-500 whitespace-nowrap">{{ $row['alt']->desa ?? '—' }}</td>
                                @endunless
                                @foreach (array_keys($kriteria) as $slug)
                                    <td class="px-4 py-4 text-center text-slate-600 tabular-nums">{{ number_format($row['norm'][$slug], 3) }}</td>
                                @endforeach
                                <td class="px-5 py-4 text-right">
                                    <span class="font-bold tabular-nums {{ $peringkat === 1 ? 'text-[#C8102E]' : 'text-[#0E2650]' }}">{{ number_format($row['skor'], 4) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Matriks keputusan (nilai asli) -->
        <div class="mb-4">
            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Rincian</span>
            <h3 class="text-lg md:text-xl font-bold text-[#0E2650] mt-2">Matriks Keputusan</h3>
            <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
            <p class="text-sm text-slate-500 mt-4 leading-relaxed max-w-2xl">Nilai crisp tiap calon sebelum normalisasi. Nilai maksimum tiap kolom (acuan normalisasi benefit) ditandai.</p>
        </div>

        <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Calon Penerima</th>
                            @foreach ($kriteria as $k)
                                <th class="text-center px-4 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500" title="{{ $k['label'] }}">{{ $k['kode'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($hasil as $row)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5 font-medium text-slate-800">{{ $row['alt']->user->name ?? '(warga terhapus)' }}</td>
                                @foreach (array_keys($kriteria) as $slug)
                                    @php $isMax = $maks[$slug] > 0 && $row['nilai'][$slug] === $maks[$slug]; @endphp
                                    <td class="px-4 py-3.5 text-center tabular-nums {{ $isMax ? 'font-bold text-[#14346B]' : 'text-slate-600' }}">
                                        {{ $row['nilai'][$slug] }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr class="bg-slate-50 border-t-2 border-slate-200">
                            <td class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Maks. kolom</td>
                            @foreach (array_keys($kriteria) as $slug)
                                <td class="px-4 py-3 text-center font-bold text-[#14346B] tabular-nums">{{ $maks[$slug] }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection
