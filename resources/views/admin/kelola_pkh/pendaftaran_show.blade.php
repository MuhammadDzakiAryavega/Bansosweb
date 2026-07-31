@extends('layouts.admin')

@section('title', 'Kelola PKH — Tinjau Pengajuan - Panel Portal PKH')
@section('page-title', 'Kelola PKH')
@section('page-subtitle', 'Rincian pengajuan pendaftaran PKH dan tindak lanjutnya.')

@section('content')

    <a href="{{ route('admin.pkh.pendaftaran.index') }}"
       class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[#14346B] transition-colors mb-6">
        <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
        Kembali ke daftar pengajuan
    </a>

    @php
        $detail = [
            ['label' => 'Desa/Kelurahan',    'value' => $pendaftaran->desa ?: '—',       'icon' => 'fa-map-location-dot'],
            ['label' => 'Alamat lengkap',    'value' => $pendaftaran->alamat,            'icon' => 'fa-location-dot'],
            ['label' => 'No. Telepon',       'value' => $pendaftaran->no_hp ?: '—',      'icon' => 'fa-phone'],
            ['label' => 'Penghasilan/bulan', 'value' => $pendaftaran->penghasilan,       'icon' => 'fa-wallet'],
            ['label' => 'Jumlah tanggungan', 'value' => $pendaftaran->jumlah_tanggungan . ' orang', 'icon' => 'fa-users'],
            ['label' => 'Kondisi rumah',     'value' => $pendaftaran->kondisi_rumah,     'icon' => 'fa-house'],
            ['label' => 'Status pekerjaan',  'value' => $pendaftaran->status_pekerjaan,  'icon' => 'fa-briefcase'],
            ['label' => 'Kepemilikan aset',  'value' => $pendaftaran->kepemilikan_aset,  'icon' => 'fa-coins'],
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Rincian -->
        <div class="lg:col-span-8">
            <section class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-6 py-6 lg:px-8 border-b border-slate-200 flex items-center gap-4">
                    <span class="w-12 h-12 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center text-lg font-semibold flex-shrink-0">
                        {{ strtoupper(substr($pendaftaran->nama, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-lg md:text-xl font-bold text-[#0E2650]">{{ $pendaftaran->nama }}</h2>
                        <p class="text-sm text-slate-500 mt-0.5 tabular-nums">NIK {{ $pendaftaran->nik }}</p>
                    </div>
                    <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border whitespace-nowrap {{ $pendaftaran->badgeStatus() }}">{{ $pendaftaran->status }}</span>
                </div>

                <dl class="divide-y divide-slate-100">
                    @foreach ($detail as $d)
                        <div class="flex items-start gap-4 px-6 py-4 lg:px-8">
                            <span class="w-8 h-8 rounded-md bg-slate-50 border border-slate-200 text-slate-400 flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $d['icon'] }} text-xs"></i>
                            </span>
                            <div class="min-w-0">
                                <dt class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ $d['label'] }}</dt>
                                <dd class="text-sm text-slate-800 mt-1 leading-relaxed">{{ $d['value'] }}</dd>
                            </div>
                        </div>
                    @endforeach
                </dl>

                <div class="px-6 py-4 lg:px-8 bg-slate-50 border-t border-slate-200 text-xs text-slate-500">
                    Diajukan {{ $pendaftaran->created_at->translatedFormat('d F Y, H:i') }} WIB
                    @if ($pendaftaran->user)
                        · dari akun <span class="font-semibold text-slate-600">{{ $pendaftaran->user->email }}</span>
                    @else
                        · <span class="text-[#C8102E] font-semibold">akun pengaju sudah dihapus</span>
                    @endif
                </div>
            </section>

            <!-- Verifikasi identitas: foto diri memegang KTP -->
            <section class="mt-6 border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-6 py-4 lg:px-8 border-b border-slate-200 flex items-center gap-2.5">
                    <i class="fas fa-id-card text-[#14346B] text-sm"></i>
                    <h3 class="font-bold text-[#0E2650]">Verifikasi Identitas</h3>
                </div>
                <div class="px-6 py-6 lg:px-8">
                    @if ($pendaftaran->foto_ktp)
                        <a href="{{ route('admin.pkh.pendaftaran.foto', [$pendaftaran, 'foto_ktp']) }}" target="_blank" rel="noopener"
                           class="group block max-w-md rounded-md overflow-hidden border border-slate-200 bg-slate-50" title="Buka foto ukuran penuh">
                            <img src="{{ route('admin.pkh.pendaftaran.foto', [$pendaftaran, 'foto_ktp']) }}" alt="Foto diri memegang KTP" loading="lazy"
                                 class="w-full object-contain group-hover:opacity-90 transition-opacity">
                        </a>
                        <p class="text-xs text-slate-500 mt-3 flex items-start gap-2 leading-relaxed">
                            <i class="fas fa-circle-info text-slate-400 mt-0.5"></i>
                            Pastikan wajah pengaju dan data pada KTP terlihat jelas serta cocok dengan nama &amp; NIK yang diisi.
                        </p>
                    @else
                        <div class="rounded-md border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-slate-400">
                            <i class="fas fa-id-card text-2xl mb-2"></i>
                            <p class="text-sm">Tidak ada foto identitas.</p>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Bukti foto kondisi rumah -->
            <section class="mt-6 border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-6 py-4 lg:px-8 border-b border-slate-200 flex items-center gap-2.5">
                    <i class="fas fa-camera text-[#14346B] text-sm"></i>
                    <h3 class="font-bold text-[#0E2650]">Bukti Kondisi Rumah</h3>
                </div>
                <div class="px-6 py-6 lg:px-8 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach (\App\Models\Pendaftaran::FOTO_RUMAH as $field => $label)
                        <div>
                            @if ($pendaftaran->{$field})
                                <a href="{{ route('admin.pkh.pendaftaran.foto', [$pendaftaran, $field]) }}" target="_blank" rel="noopener"
                                   class="group block aspect-square rounded-md overflow-hidden border border-slate-200 bg-slate-50"
                                   title="Buka {{ $label }} ukuran penuh">
                                    <img src="{{ route('admin.pkh.pendaftaran.foto', [$pendaftaran, $field]) }}" alt="{{ $label }}"
                                         class="w-full h-full object-cover group-hover:opacity-90 transition-opacity" loading="lazy">
                                </a>
                            @else
                                <div class="aspect-square rounded-md border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center text-slate-300">
                                    <i class="fas fa-image text-xl"></i>
                                </div>
                            @endif
                            <p class="text-[11px] font-semibold text-slate-600 mt-2 text-center leading-tight">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            @if ($pendaftaran->catatan_admin)
                <div class="mt-6 border border-amber-200 bg-amber-50 rounded-lg px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 mb-1">Catatan Petugas</p>
                    <p class="text-sm text-amber-800 leading-relaxed">{{ $pendaftaran->catatan_admin }}</p>
                </div>
            @endif
        </div>

        <!-- Tindak lanjut -->
        <aside class="lg:col-span-4">
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden sticky top-6">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="font-bold text-[#0E2650]">Tindak Lanjut</h3>
                </div>
                <div class="px-6 py-6 space-y-4">

                    @if ($sudahCalon)
                        <div class="flex items-start gap-3 rounded-md border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
                            <i class="fas fa-circle-check mt-0.5"></i>
                            <span class="leading-relaxed">Sudah terdaftar sebagai calon penerima.</span>
                        </div>
                        <a href="{{ route('admin.pkh.penilaian.index') }}"
                           class="w-full inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-5 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                            <i class="fas fa-clipboard-check text-xs"></i> Ke Penilaian Calon
                        </a>
                    @endif

                    @if ($pendaftaran->status !== 'Diverifikasi')
                        <form method="POST" action="{{ route('admin.pkh.pendaftaran.verifikasi', $pendaftaran) }}"
                              onsubmit="return confirm('Verifikasi pengajuan ini dan tambahkan {{ $pendaftaran->nama }} sebagai calon penerima?');">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white px-5 py-3 rounded-md text-sm font-semibold hover:bg-emerald-700 transition-colors">
                                <i class="fas fa-user-check text-xs"></i> Verifikasi &amp; Jadikan Calon
                            </button>
                        </form>
                    @endif

                    @if ($pendaftaran->status !== 'Ditolak')
                        <div x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                    class="w-full inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-5 py-3 rounded-md text-sm font-semibold hover:border-[#C8102E] hover:text-[#C8102E] transition-colors">
                                <i class="fas fa-xmark text-xs"></i> Tolak Pengajuan
                            </button>
                            <form x-show="open" x-cloak x-transition method="POST"
                                  action="{{ route('admin.pkh.pendaftaran.tolak', $pendaftaran) }}" class="mt-3">
                                @csrf
                                <label for="catatan_admin" class="block text-xs font-semibold text-slate-600 mb-2">Alasan penolakan <span class="text-slate-400 font-normal">(opsional)</span></label>
                                <textarea id="catatan_admin" name="catatan_admin" rows="3" maxlength="500"
                                          placeholder="mis. Data tidak sesuai hasil verifikasi lapangan"
                                          class="w-full border border-slate-300 rounded-md px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#C8102E] focus:ring-1 focus:ring-[#C8102E] transition resize-y"></textarea>
                                <button type="submit"
                                        class="mt-2 w-full inline-flex items-center justify-center gap-2 bg-[#C8102E] text-white px-5 py-2.5 rounded-md text-sm font-semibold hover:bg-[#a50d26] transition-colors">
                                    Konfirmasi Penolakan
                                </button>
                            </form>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.pkh.pendaftaran.destroy', $pendaftaran) }}"
                          onsubmit="return confirm('Hapus pengajuan {{ $pendaftaran->nama }} secara permanen?');"
                          class="pt-2 border-t border-slate-100">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 text-slate-500 px-5 py-2.5 rounded-md text-sm font-semibold hover:text-[#C8102E] transition-colors">
                            <i class="fas fa-trash text-xs"></i> Hapus Pengajuan
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </div>

@endsection
