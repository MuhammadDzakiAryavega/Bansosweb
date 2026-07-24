@extends('layouts.admin')

@section('title', 'Kelola PKH — Pendaftaran Masuk - Admin Portal PKH')
@section('page-title', 'Kelola PKH')
@section('page-subtitle', 'Pengajuan warga yang ingin menjadi calon penerima PKH. Tinjau lalu verifikasi untuk menambahkannya ke penilaian SAW.')

@section('content')

    @php
        $kartu = [
            ['label' => 'Total Pengajuan', 'value' => $statistik['total'],        'desc' => 'Seluruh pendaftaran masuk', 'icon' => 'fa-inbox'],
            ['label' => 'Baru',            'value' => $statistik['baru'],         'desc' => 'Menunggu ditinjau',         'icon' => 'fa-hourglass-half', 'accent' => true],
            ['label' => 'Diverifikasi',    'value' => $statistik['diverifikasi'], 'desc' => 'Menjadi calon penerima',    'icon' => 'fa-user-check'],
            ['label' => 'Ditolak',         'value' => $statistik['ditolak'],      'desc' => 'Tidak memenuhi syarat',     'icon' => 'fa-circle-xmark'],
        ];
    @endphp

    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-slate-200 border border-slate-200 rounded-lg overflow-hidden mb-8">
        @foreach($kartu as $k)
            <div class="reveal bg-white px-6 py-6" style="transition-delay: {{ $loop->index * 60 }}ms">
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">{{ $k['label'] }}</dt>
                    <span class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0 {{ !empty($k['accent']) ? 'bg-[#C8102E]/5 text-[#C8102E]' : 'bg-[#14346B]/5 text-[#14346B]' }}">
                        <i class="fas {{ $k['icon'] }} text-xs"></i>
                    </span>
                </div>
                <dd class="text-3xl font-bold tabular-nums mt-3 {{ !empty($k['accent']) && $k['value'] > 0 ? 'text-[#C8102E]' : 'text-[#0E2650]' }}">{{ $k['value'] }}</dd>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $k['desc'] }}</p>
            </div>
        @endforeach
    </dl>

    <!-- Filter -->
    <div class="border border-slate-200 rounded-lg bg-white px-5 py-5 mb-6">
        <form method="GET" action="{{ route('admin.pkh.pendaftaran.index') }}" class="flex flex-col md:flex-row md:flex-wrap gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <label for="cari" class="sr-only">Cari pengajuan</label>
                <input type="text" id="cari" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau NIK..."
                       class="w-full pl-10 pr-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
            </div>
            <label for="desa" class="sr-only">Saring desa</label>
            <select name="desa" id="desa" onchange="this.form.submit()"
                    class="px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                <option value="">Semua Desa</option>
                @foreach ($desaList as $desa)
                    <option value="{{ $desa }}" @selected(request('desa') === $desa)>{{ $desa }}</option>
                @endforeach
            </select>
            <label for="status" class="sr-only">Saring status</label>
            <select name="status" id="status" onchange="this.form.submit()"
                    class="px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                <option value="">Semua Status</option>
                @foreach ($statusList as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-5 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                <i class="fas fa-filter text-xs"></i> Terapkan
            </button>
            @if (request('cari') || request('status') || request('desa'))
                <a href="{{ route('admin.pkh.pendaftaran.index') }}"
                   class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-5 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                    <i class="fas fa-rotate-left text-xs"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel -->
    <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Pengaju</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">NIK</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Desa</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tanggal</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="text-right px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($pendaftaran as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 flex-shrink-0 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center text-sm font-semibold">
                                        {{ strtoupper(substr($item->nama, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900 truncate">{{ $item->nama }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ \Illuminate\Support\Str::limit($item->alamat, 40) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 tabular-nums whitespace-nowrap">{{ $item->nik }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-md border border-slate-200 bg-slate-50 text-slate-600">
                                    <i class="fas fa-location-dot text-[9px] text-slate-400"></i> {{ $item->desa ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $item->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border whitespace-nowrap {{ $item->badgeStatus() }}">{{ $item->status }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('admin.pkh.pendaftaran.show', $item) }}"
                                       class="inline-flex items-center gap-2 border border-slate-300 text-slate-700 px-4 py-2 rounded-md text-xs font-semibold hover:bg-[#14346B] hover:border-[#14346B] hover:text-white transition-colors">
                                        <i class="fas fa-eye text-xs"></i> Tinjau
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <span class="w-12 h-12 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-xl mb-4">
                                    <i class="fas fa-inbox"></i>
                                </span>
                                <p class="font-semibold text-slate-800">Belum ada pengajuan</p>
                                <p class="text-sm text-slate-500 mt-1">Pendaftaran PKH dari warga akan tampil di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pendaftaran->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $pendaftaran->links() }}</div>
        @endif
    </div>

@endsection
