@extends('layouts.admin')

@section('title', 'Kelola Pengaduan - Panel Portal PKH')
@section('page-title', 'Kelola Pengaduan')
@section('page-subtitle', 'Pantau dan tindak lanjuti pengaduan yang masuk dari masyarakat.')

@section('content')

    <!-- ================= RINGKASAN STATUS ================= -->
    @php
        $kartu_statistik = [
            [
                'label' => 'Total Pengaduan',
                'value' => $statistik['total'],
                'desc'  => 'Seluruh laporan masuk',
                'icon'  => 'fa-bullhorn',
            ],
            [
                'label' => 'Baru',
                'value' => $statistik['baru'],
                'desc'  => 'Belum ditinjau petugas',
                'icon'  => 'fa-envelope-open-text',
                'accent' => true,
            ],
            [
                'label' => 'Dalam Proses',
                'value' => $statistik['dalam_proses'],
                'desc'  => 'Sedang ditindaklanjuti',
                'icon'  => 'fa-spinner',
            ],
            [
                'label' => 'Selesai',
                'value' => $statistik['selesai'],
                'desc'  => 'Penanganan tuntas',
                'icon'  => 'fa-circle-check',
            ],
        ];
    @endphp

    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-slate-200 border border-slate-200 rounded-lg overflow-hidden mb-8">
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

    <!-- ================= FILTER & PENCARIAN ================= -->
    <div class="border border-slate-200 rounded-lg bg-white px-5 py-5 mb-6">
        <form method="GET" action="{{ route('admin.pengaduan.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <label for="cari" class="sr-only">Cari pengaduan</label>
                <input type="text" id="cari" name="cari" value="{{ request('cari') }}"
                       placeholder="Cari nama, surel, atau judul pengaduan..."
                       class="w-full pl-10 pr-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
            </div>
            <label for="status" class="sr-only">Saring berdasarkan status</label>
            <select name="status" id="status" onchange="this.form.submit()"
                    class="px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                <option value="">Semua Status</option>
                @foreach ($statusList as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                <i class="fas fa-filter text-xs"></i> Terapkan
            </button>
            @if (request('cari') || request('status'))
                <a href="{{ route('admin.pengaduan.index') }}"
                   class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-6 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                    <i class="fas fa-rotate-left text-xs"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- ================= DAFTAR PENGADUAN ================= -->
    <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Pengadu</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Judul Pengaduan</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tanggal</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="text-right px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($pengaduans as $item)
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
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900">{{ $item->nama_pengadu }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $item->email_pengadu }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-slate-700">{{ \Illuminate\Support\Str::limit($item->judul_pengaduan, 45) }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $item->tanggal_pengaduan->translatedFormat('d M Y, H:i') }} WIB
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.pengaduan.updateStatus', $item->id_pengaduan) }}">
                                    @csrf
                                    @method('PATCH')
                                    <label for="status-{{ $item->id_pengaduan }}" class="sr-only">Ubah status pengaduan</label>
                                    <select name="status_pengaduan" id="status-{{ $item->id_pengaduan }}" onchange="this.form.submit()"
                                            class="text-[11px] font-semibold px-3 py-1.5 rounded-md border cursor-pointer outline-none {{ $badge }}">
                                        @foreach ($statusList as $status)
                                            <option value="{{ $status }}" @selected($item->status_pengaduan === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.pengaduan.show', $item->id_pengaduan) }}"
                                       class="w-9 h-9 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-[#14346B] hover:border-[#14346B] hover:text-white transition-colors"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.pengaduan.destroy', $item->id_pengaduan) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus pengaduan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-9 h-9 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-[#C8102E] hover:border-[#C8102E] hover:text-white transition-colors"
                                                title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <span class="w-12 h-12 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-xl mb-4">
                                    <i class="fas fa-inbox"></i>
                                </span>
                                <p class="font-semibold text-slate-800">Tidak ada pengaduan ditemukan</p>
                                <p class="text-sm text-slate-500 mt-1">Coba ubah filter atau kata kunci pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pengaduans->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $pengaduans->links() }}
            </div>
        @endif
    </div>

@endsection
