@extends('layouts.admin')

@section('title', 'Kelola Arsip - Admin Portal PKH')
@section('page-title', 'Kelola Arsip')
@section('page-subtitle', 'Pencatatan surat masuk, surat keluar, dan dokumen penting Dinas Sosial. Arsip beserta lampirannya hanya dapat diakses oleh administrator.')

@section('content')

    <!-- ================= RINGKASAN ================= -->
    @php
        $kartu_statistik = [
            [
                'label' => 'Total Arsip',
                'value' => $statistik['total'],
                'desc'  => 'Seluruh dokumen tercatat',
                'icon'  => 'fa-folder-open',
            ],
            [
                'label' => 'Dipublikasikan',
                'value' => $statistik['terbit'],
                'desc'  => 'Sudah disahkan & final',
                'icon'  => 'fa-circle-check',
            ],
            [
                'label' => 'Draf',
                'value' => $statistik['draft'],
                'desc'  => 'Masih dalam pengerjaan',
                'icon'  => 'fa-pen-ruler',
                'accent' => true,
            ],
            [
                'label' => 'Berlampiran',
                'value' => $statistik['lampiran'],
                'desc'  => 'Memiliki berkas dokumen',
                'icon'  => 'fa-paperclip',
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
            <form method="GET" action="{{ route('admin.arsip.index') }}" class="flex flex-col md:flex-row md:flex-wrap gap-3 flex-1">
                <div class="relative flex-1 min-w-[220px]">
                    <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <label for="cari" class="sr-only">Cari arsip</label>
                    <input type="text" id="cari" name="cari" value="{{ request('cari') }}"
                           placeholder="Cari nomor, judul, atau deskripsi arsip..."
                           class="w-full pl-10 pr-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                </div>
                <label for="klasifikasi" class="sr-only">Saring klasifikasi</label>
                <select name="klasifikasi" id="klasifikasi" onchange="this.form.submit()"
                        class="px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                    <option value="">Semua Klasifikasi</option>
                    @foreach ($klasifikasiList as $klasifikasi)
                        <option value="{{ $klasifikasi }}" @selected(request('klasifikasi') === $klasifikasi)>{{ $klasifikasi }}</option>
                    @endforeach
                </select>
                <label for="tahun" class="sr-only">Saring tahun dokumen</label>
                <select name="tahun" id="tahun" onchange="this.form.submit()"
                        class="px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}" @selected((string) request('tahun') === (string) $tahun)>{{ $tahun }}</option>
                    @endforeach
                </select>
                <label for="status" class="sr-only">Saring status</label>
                <select name="status" id="status" onchange="this.form.submit()"
                        class="px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                    <option value="">Semua Status</option>
                    @foreach ($statusList as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ $status === 'Published' ? 'Dipublikasikan' : 'Draf' }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-5 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                    <i class="fas fa-filter text-xs"></i> Terapkan
                </button>
                @if (request('cari') || request('status') || request('klasifikasi') || request('tahun'))
                    <a href="{{ route('admin.arsip.index') }}"
                       class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-5 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                        <i class="fas fa-rotate-left text-xs"></i> Reset
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.arsip.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors lg:border-l lg:border-slate-200 lg:ml-1 flex-shrink-0">
                <i class="fas fa-plus text-xs"></i> Tambah Arsip
            </a>
        </div>
    </div>

    <!-- ================= DAFTAR ARSIP ================= -->
    <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Arsip</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Klasifikasi</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tgl Dokumen</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Lampiran</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="text-right px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($arsips as $item)
                        @php
                            $badge = $item->sudahTerbit()
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                : 'bg-amber-50 text-amber-700 border-amber-200';
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors align-top">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <span class="w-10 h-10 rounded-md border border-slate-200 bg-[#14346B]/5 text-[#14346B] flex items-center justify-center flex-shrink-0">
                                        <i class="fas {{ $item->adaLampiran() ? $item->ikonLampiran() : 'fa-folder' }} text-sm"></i>
                                    </span>
                                    <div class="min-w-0 max-w-md">
                                        <p class="text-[11px] font-semibold tracking-wider text-slate-400 tabular-nums">{{ $item->nomor_arsip }}</p>
                                        <p class="font-semibold text-slate-900 mt-0.5">{{ \Illuminate\Support\Str::limit($item->judul_arsip, 60) }}</p>
                                        @if ($item->deskripsi_tambahan)
                                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $item->ringkasan(80) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20 whitespace-nowrap">
                                    {{ $item->klasifikasi }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $item->tgl_dokumen->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->adaLampiran())
                                    <a href="{{ route('admin.arsip.unduh', $item->id_arsip) }}"
                                       class="group inline-flex items-center gap-2 text-[#14346B] font-semibold hover:text-[#0E2650] transition-colors"
                                       title="Unduh {{ $item->lampiran_nama }}">
                                        <i class="fas fa-download text-[10px]"></i>
                                        <span class="flex flex-col leading-tight">
                                            <span class="text-xs">{{ \Illuminate\Support\Str::limit($item->lampiran_nama, 24) }}</span>
                                            <span class="text-[11px] font-medium text-slate-400">{{ $item->ukuranLampiran() }}</span>
                                        </span>
                                    </a>
                                @else
                                    <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border bg-slate-50 text-slate-500 border-slate-200 whitespace-nowrap">
                                        Tanpa berkas
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border {{ $badge }} whitespace-nowrap">
                                    {{ $item->labelStatus() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.arsip.edit', $item->id_arsip) }}"
                                       class="w-9 h-9 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-[#14346B] hover:border-[#14346B] hover:text-white transition-colors"
                                       title="Ubah">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.arsip.destroy', $item->id_arsip) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus arsip {{ $item->nomor_arsip }}? Lampirannya ikut terhapus.');">
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
                            <td colspan="6" class="px-6 py-16 text-center">
                                <span class="w-12 h-12 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-xl mb-4">
                                    <i class="fas fa-folder-open"></i>
                                </span>
                                <p class="font-semibold text-slate-800">Belum ada arsip</p>
                                <p class="text-sm text-slate-500 mt-1">Mulai dengan menekan tombol <span class="font-semibold">Tambah Arsip</span>.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($arsips->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $arsips->links() }}
            </div>
        @endif
    </div>

@endsection
