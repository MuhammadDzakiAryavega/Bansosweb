@extends('layouts.admin')

@section('title', 'Kelola Pengguna - Admin Portal PKH')
@section('page-title', 'Kelola Pengguna')
@section('page-subtitle', 'Kelola akun masyarakat dan administrator yang memiliki akses ke portal PKH.')

@section('content')

    <!-- ================= RINGKASAN ================= -->
    @php
        $kartu_statistik = [
            [
                'label' => 'Total Akun',
                'value' => $statistik['total'],
                'desc'  => 'Seluruh akun terdaftar',
                'icon'  => 'fa-users',
            ],
            [
                'label' => 'Masyarakat',
                'value' => $statistik['masyarakat'],
                'desc'  => 'Dapat mengirim pengaduan',
                'icon'  => 'fa-user',
            ],
            [
                'label' => 'Administrator',
                'value' => $statistik['admin'],
                'desc'  => 'Memiliki akses panel admin',
                'icon'  => 'fa-user-shield',
                'accent' => true,
            ],
            [
                'label' => 'Akun Baru',
                'value' => $statistik['baru'],
                'desc'  => 'Terdaftar dalam 30 hari terakhir',
                'icon'  => 'fa-user-plus',
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
                <dd class="text-3xl font-bold tabular-nums mt-3 {{ !empty($kartu['accent']) ? 'text-[#C8102E]' : 'text-[#0E2650]' }}">
                    {{ $kartu['value'] }}
                </dd>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $kartu['desc'] }}</p>
            </div>
        @endforeach
    </dl>

    <!-- ================= FILTER & TOMBOL TAMBAH ================= -->
    <div class="border border-slate-200 rounded-lg bg-white px-5 py-5 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center gap-3">
            <form method="GET" action="{{ route('admin.user.index') }}" class="flex flex-col md:flex-row gap-3 flex-1">
                <div class="relative flex-1">
                    <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <label for="cari" class="sr-only">Cari pengguna</label>
                    <input type="text" id="cari" name="cari" value="{{ request('cari') }}"
                           placeholder="Cari nama, surel, atau NIK..."
                           class="w-full pl-10 pr-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                </div>
                <label for="role" class="sr-only">Saring peran</label>
                <select name="role" id="role" onchange="this.form.submit()"
                        class="px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                    <option value="">Semua Peran</option>
                    @foreach ($roleList as $role)
                        <option value="{{ $role }}" @selected(request('role') === $role)>
                            {{ $role === 'admin' ? 'Administrator' : 'Masyarakat' }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-5 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                    <i class="fas fa-filter text-xs"></i> Terapkan
                </button>
                @if (request('cari') || request('role'))
                    <a href="{{ route('admin.user.index') }}"
                       class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 px-5 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                        <i class="fas fa-rotate-left text-xs"></i> Reset
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.user.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors lg:border-l lg:border-slate-200 lg:ml-1 flex-shrink-0">
                <i class="fas fa-user-plus text-xs"></i> Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- ================= DAFTAR PENGGUNA ================= -->
    <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Pengguna</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">NIK</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Peran</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Pengaduan</th>
                        <th class="text-left px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Terdaftar</th>
                        <th class="text-right px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($users as $item)
                        @php
                            $badge = $item->isAdmin()
                                ? 'bg-[#C8102E]/5 text-[#C8102E] border-[#C8102E]/20'
                                : 'bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20';
                            $akunSendiri = $item->is(Auth::user());
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors align-top">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-4">
                                    <span class="w-10 h-10 rounded-md flex items-center justify-center flex-shrink-0 font-semibold text-sm {{ $item->isAdmin() ? 'bg-[#C8102E] text-white' : 'bg-[#14346B] text-white' }}">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0 max-w-xs">
                                        <p class="font-semibold text-slate-900">
                                            {{ $item->name }}
                                            @if ($akunSendiri)
                                                <span class="ml-1 text-[10px] font-semibold uppercase tracking-wider text-slate-400">(Anda)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1 break-all">{{ $item->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 tabular-nums whitespace-nowrap">
                                {{ $item->nik ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border {{ $badge }} whitespace-nowrap">
                                    {{ $item->labelRole() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 tabular-nums whitespace-nowrap">
                                {{ $item->pengaduans_count }} pengaduan
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $item->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.user.edit', $item->id) }}"
                                       class="w-9 h-9 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-[#14346B] hover:border-[#14346B] hover:text-white transition-colors"
                                       title="Ubah">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    @if ($akunSendiri)
                                        <span class="w-9 h-9 rounded-md border border-slate-200 text-slate-300 flex items-center justify-center cursor-not-allowed"
                                              title="Akun sendiri tidak dapat dihapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.user.destroy', $item->id) }}"
                                              onsubmit="return confirm('Yakin ingin menghapus akun {{ $item->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-9 h-9 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:bg-[#C8102E] hover:border-[#C8102E] hover:text-white transition-colors"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <span class="w-12 h-12 mx-auto rounded-md bg-slate-50 border border-slate-200 text-slate-300 flex items-center justify-center text-xl mb-4">
                                    <i class="fas fa-users"></i>
                                </span>
                                <p class="font-semibold text-slate-800">Belum ada pengguna</p>
                                <p class="text-sm text-slate-500 mt-1">Tambahkan akun melalui tombol <span class="font-semibold">Tambah Pengguna</span>.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@endsection
