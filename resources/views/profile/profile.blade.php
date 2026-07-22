@extends('layouts.layout')
@section('title', 'Profil Akun - Portal PKH Dinas Sosial Kabupaten Mukomuko')
@section('content')
@php
    $user      = Auth::user();
    $inputBase = 'w-full border rounded-md px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition';
    $inputOk   = 'border-slate-300 focus:border-[#14346B] focus:ring-[#14346B]';
    $inputErr  = 'border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E]';
@endphp

    <!-- ================= JUDUL HALAMAN ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <nav aria-label="Breadcrumb" class="text-xs text-slate-500">
                <ol class="flex items-center gap-2">
                    <li><a href="{{ url('/') }}" class="hover:text-[#14346B] transition-colors">Beranda</a></li>
                    <li class="text-slate-300">/</li>
                    <li class="text-slate-700 font-medium">Profil Akun</li>
                </ol>
            </nav>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-block w-7 border border-slate-300 overflow-hidden">
                    <span class="block h-[7px] bg-[#C8102E]"></span>
                    <span class="block h-[7px] bg-white"></span>
                </span>
                <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    Akun Pengguna
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-[#0E2650] leading-[1.2] tracking-tight">
                Profil Akun
            </h1>
            <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>
            <p class="mt-6 text-base text-slate-600 leading-relaxed max-w-3xl">
                Kelola data akun yang digunakan untuk mengakses layanan Program Keluarga Harapan pada portal ini.
            </p>
        </div>
    </section>

    <!-- ================= ISI ================= -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            @if (session('status') === 'profile-updated')
                <div class="mb-6 border border-emerald-200 bg-emerald-50 rounded-md px-5 py-4 flex items-start gap-3">
                    <i class="fas fa-circle-check text-emerald-600 mt-0.5"></i>
                    <p class="text-sm text-emerald-800 font-semibold">Data akun berhasil diperbarui.</p>
                </div>
            @elseif (session('status') === 'password-updated')
                <div class="mb-6 border border-emerald-200 bg-emerald-50 rounded-md px-5 py-4 flex items-start gap-3">
                    <i class="fas fa-circle-check text-emerald-600 mt-0.5"></i>
                    <p class="text-sm text-emerald-800 font-semibold">Kata sandi berhasil diubah.</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 border border-rose-200 bg-rose-50 rounded-md px-5 py-4">
                    <p class="text-sm text-[#C8102E] font-semibold flex items-start gap-2 mb-1">
                        <i class="fas fa-circle-exclamation mt-0.5"></i>
                        <span>Perubahan belum tersimpan. Periksa kembali isian berikut:</span>
                    </p>
                    <ul class="text-sm text-[#C8102E]/90 list-disc pl-9 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- Kartu identitas akun -->
                <aside class="lg:col-span-4">
                    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                        <div class="bg-[#14346B] px-6 py-5 flex items-center gap-4">
                            <span class="w-12 h-12 rounded-md bg-white/10 border border-white/20 text-white flex items-center justify-center text-lg font-bold flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-white font-semibold truncate">{{ $user->name }}</p>
                                <p class="text-blue-100/70 text-xs truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        <dl class="divide-y divide-slate-100 text-sm">
                            <div class="px-6 py-3.5 flex items-start justify-between gap-4">
                                <dt class="text-slate-500">NIK</dt>
                                <dd class="font-semibold text-slate-800 text-right tabular-nums">{{ $user->nik ?? '-' }}</dd>
                            </div>
                            <div class="px-6 py-3.5 flex items-start justify-between gap-4">
                                <dt class="text-slate-500">Jenis Akun</dt>
                                <dd>
                                    <span class="inline-flex items-center gap-2 text-[11px] font-semibold px-3 py-1.5 rounded-md border {{ $user->isAdmin() ? 'border-[#14346B]/20 bg-[#14346B]/5 text-[#14346B]' : 'border-slate-200 bg-slate-50 text-slate-600' }}">
                                        {{ $user->isAdmin() ? 'Administrator' : 'Masyarakat' }}
                                    </span>
                                </dd>
                            </div>
                            @if ($user->created_at)
                            <div class="px-6 py-3.5 flex items-start justify-between gap-4">
                                <dt class="text-slate-500">Terdaftar Sejak</dt>
                                <dd class="font-semibold text-slate-800 text-right">{{ $user->created_at->translatedFormat('d F Y') }}</dd>
                            </div>
                            @endif
                        </dl>

                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/60">
                            <a href="{{ route('pengaduan.riwayat') }}" class="text-sm font-semibold text-[#14346B] inline-flex items-center gap-2 hover:gap-3 transition-all">
                                Riwayat Pengaduan Saya <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </aside>

                <!-- Formulir -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Data akun -->
                    <div class="bg-white border border-slate-200 rounded-lg">
                        <div class="flex items-center gap-3 px-7 py-5 border-b border-slate-200">
                            <span class="w-9 h-9 flex items-center justify-center rounded-md border border-[#14346B]/20 text-[#14346B] flex-shrink-0">
                                <i class="fas fa-user text-sm"></i>
                            </span>
                            <div>
                                <h2 class="font-semibold text-slate-900">Data Akun</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Perbarui nama yang ditampilkan pada portal.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="px-7 py-6 space-y-5">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                                    <input type="text" id="name" name="name" autocomplete="name"
                                           value="{{ old('name', $user->name) }}"
                                           class="{{ $inputBase }} {{ $errors->has('name') ? $inputErr : $inputOk }}">
                                    @error('name')
                                        <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label for="profile-email" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Surel</label>
                                        <input type="email" id="profile-email" value="{{ $user->email }}" disabled autocomplete="off"
                                               class="w-full border border-slate-200 rounded-md px-4 py-3 text-sm bg-slate-50 text-slate-500">
                                    </div>
                                    <div>
                                        <label for="profile-nik" class="block text-sm font-semibold text-slate-700 mb-2">NIK</label>
                                        <input type="text" id="profile-nik" value="{{ $user->nik ?? '-' }}" disabled autocomplete="off"
                                               class="w-full border border-slate-200 rounded-md px-4 py-3 text-sm bg-slate-50 text-slate-500 tabular-nums">
                                    </div>
                                </div>

                                <p class="text-xs text-slate-500 leading-relaxed border-t border-slate-100 pt-4">
                                    Alamat surel dan NIK digunakan sebagai data verifikasi kepesertaan sehingga tidak dapat
                                    diubah sendiri. Hubungi petugas Dinas Sosial apabila terdapat kekeliruan data.
                                </p>
                            </div>

                            <div class="px-7 py-5 border-t border-slate-200 bg-slate-50/60">
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Ganti kata sandi -->
                    <div class="bg-white border border-slate-200 rounded-lg">
                        <div class="flex items-center gap-3 px-7 py-5 border-b border-slate-200">
                            <span class="w-9 h-9 flex items-center justify-center rounded-md border border-[#14346B]/20 text-[#14346B] flex-shrink-0">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <div>
                                <h2 class="font-semibold text-slate-900">Ganti Kata Sandi</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Gunakan kata sandi baru minimal 8 karakter.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PUT')

                            <!-- Username tersembunyi: membantu browser mengaitkan form ini dengan akun yang benar -->
                            <input type="text" name="username" value="{{ $user->email }}" autocomplete="username" class="hidden">

                            <div class="px-7 py-6 space-y-5">
                                <div>
                                    <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-2">Kata Sandi Saat Ini</label>
                                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                                           class="{{ $inputBase }} {{ $errors->has('current_password') ? $inputErr : $inputOk }}">
                                    @error('current_password')
                                        <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label for="new_password" class="block text-sm font-semibold text-slate-700 mb-2">Kata Sandi Baru</label>
                                        <input type="password" id="new_password" name="password" autocomplete="new-password"
                                               placeholder="Minimal 8 karakter"
                                               class="{{ $inputBase }} {{ $errors->has('password') ? $inputErr : $inputOk }}">
                                        @error('password')
                                            <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                                               placeholder="Ulangi kata sandi baru"
                                               class="{{ $inputBase }} {{ $inputOk }}">
                                    </div>
                                </div>
                            </div>

                            <div class="px-7 py-5 border-t border-slate-200 bg-slate-50/60">
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                                    Ubah Kata Sandi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
