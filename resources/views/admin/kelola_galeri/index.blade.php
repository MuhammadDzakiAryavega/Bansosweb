@extends('layouts.admin')

@section('title', 'Kelola Galeri - Admin Portal PKH')
@section('page-title', 'Kelola Galeri')
@section('page-subtitle', 'Kelola dokumentasi foto kegiatan Program Keluarga Harapan yang tampil di portal pengguna.')

@section('content')

    <!-- ================= RINGKASAN ================= -->
    @php
        $kartu_statistik = [
            [
                'label' => 'Total Kegiatan',
                'value' => $statistik['total'],
                'desc'  => 'Seluruh galeri tersimpan',
                'icon'  => 'fa-images',
            ],
            [
                'label' => 'Total Foto',
                'value' => $statistik['foto'],
                'desc'  => 'Berkas dokumentasi terunggah',
                'icon'  => 'fa-camera',
            ],
            [
                'label' => 'Bulan Ini',
                'value' => $statistik['bulan_ini'],
                'desc'  => 'Kegiatan pada ' . now()->translatedFormat('F Y'),
                'icon'  => 'fa-calendar-day',
                'accent' => true,
            ],
            [
                'label' => 'Tahun Ini',
                'value' => $statistik['tahun_ini'],
                'desc'  => 'Kegiatan sepanjang ' . now()->year,
                'icon'  => 'fa-calendar-check',
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

    <!-- ================= FILTER & TOMBOL TAMBAH ================= -->
    <div class="border border-slate-200 rounded-lg bg-white px-5 py-5 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
            <form method="GET" action="{{ route('admin.galeri.index') }}" class="flex flex-col md:flex-row gap-3 flex-1">
                <div class="relative flex-1">
                    <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <label for="cari" class="sr-only">Cari kegiatan</label>
                    <input type="text" id="cari" name="cari" value="{{ request('cari') }}"
                           placeholder="Cari judul atau deskripsi kegiatan..."
                           class="w-full pl-10 pr-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                </div>
                <label for="tahun" class="sr-only">Saring tahun pelaksanaan</label>
                <select name="tahun" id="tahun" onchange="this.form.submit()"
                        class="px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}" @selected((string) request('tahun') === (string) $tahun)>{{ $tahun }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-5 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                    <i class="fas fa-filter text-xs"></i> Terapkan
                </button>
                @if (request('cari') || request('tahun'))
                    <a href="{{ route('admin.galeri.index') }}"
                       class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-5 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                        <i class="fas fa-rotate-left text-xs"></i> Reset
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.galeri.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors lg:border-l lg:border-slate-200 lg:ml-1 flex-shrink-0">
                <i class="fas fa-plus text-xs"></i> Tambah Galeri
            </a>
        </div>
    </div>

    <!-- ================= DAFTAR GALERI ================= -->
    <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kegiatan</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tanggal Pelaksanaan</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Dokumentasi</th>
                        <th class="text-right px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($galeris as $item)
                        @php $sampul = $item->sampul(); @endphp
                        <tr class="hover:bg-slate-50 transition-colors align-top">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    @if ($sampul)
                                        <img src="{{ $sampul->path }}" alt=""
                                             class="w-16 h-12 object-cover rounded-md border border-slate-200 flex-shrink-0">
                                    @else
                                        <span class="w-16 h-12 rounded-md border border-slate-200 bg-slate-50 text-slate-300 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-image text-xs"></i>
                                        </span>
                                    @endif
                                    <div class="min-w-0 max-w-md">
                                        <p class="font-semibold text-slate-900">{{ \Illuminate\Support\Str::limit($item->judul_kegiatan, 60) }}</p>
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $item->ringkasan(80) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                {{ $item->tgl_pelaksanaan->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border whitespace-nowrap {{ $item->fotos_count > 0 ? 'bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    <i class="fas fa-camera mr-1.5 text-[10px]"></i>
                                    {{ $item->fotos_count }} foto
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('galeri.show', $item->slug) }}" target="_blank" rel="noopener"
                                       class="w-9 h-9 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-[#14346B] hover:border-[#14346B] hover:text-white transition-colors"
                                       title="Lihat di portal">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.galeri.edit', $item->id_galeri) }}"
                                       class="w-9 h-9 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-[#14346B] hover:border-[#14346B] hover:text-white transition-colors"
                                       title="Ubah">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.galeri.destroy', $item->id_galeri) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus galeri ini beserta seluruh fotonya?');">
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
                            <td colspan="4" class="px-6 py-16 text-center">
                                <span class="w-12 h-12 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-xl mb-4">
                                    <i class="fas fa-images"></i>
                                </span>
                                <p class="font-semibold text-slate-800">Belum ada galeri kegiatan</p>
                                <p class="text-sm text-slate-500 mt-1">Mulai dengan menekan tombol <span class="font-semibold">Tambah Galeri</span>.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($galeris->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $galeris->links() }}
            </div>
        @endif
    </div>

@endsection
