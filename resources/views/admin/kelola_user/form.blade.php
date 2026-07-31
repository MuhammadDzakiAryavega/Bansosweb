{{-- Formulir pengguna. Dipakai bersama oleh halaman tambah & ubah.
     Variabel: $user (null saat tambah), $aksi, $metode, $labelTombol, $roleList. --}}

@php
    $nilai = fn ($kolom, $bawaan = null) => old($kolom, $user->{$kolom} ?? $bawaan);
    $akunSendiri = isset($user) && $user->is(Auth::user());
@endphp

@if ($errors->any())
    <div class="mb-6 flex items-start gap-3 rounded-md border border-rose-200 bg-rose-50 text-rose-700 px-5 py-4 text-sm">
        <i class="fas fa-circle-exclamation mt-0.5"></i>
        <div>
            <p class="font-semibold">Periksa kembali isian berikut:</p>
            <ul class="mt-1.5 space-y-1 list-disc list-inside leading-relaxed">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ $aksi }}">
    @csrf
    @if ($metode !== 'POST')
        @method($metode)
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

        <!-- ================= DATA DIRI ================= -->
        <div class="lg:col-span-8 space-y-6">
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Data Pengguna</h2>
                </div>

                <div class="px-6 py-6 lg:px-7 space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Lengkap <span class="text-[#C8102E]">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ $nilai('name') }}"
                               maxlength="255" placeholder="Contoh: Siti Aminah"
                               class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('name') border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E] @else border-slate-300 focus:border-[#14346B] focus:ring-[#14346B] @enderror">
                        @error('name')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nik" class="block text-sm font-semibold text-slate-700 mb-2">
                            NIK <span class="text-[#C8102E]">*</span>
                        </label>
                        <input type="text" id="nik" name="nik" value="{{ $nilai('nik') }}"
                               inputmode="numeric" maxlength="16" placeholder="16 digit angka sesuai KTP"
                               class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 tabular-nums placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('nik') border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E] @else border-slate-300 focus:border-[#14346B] focus:ring-[#14346B] @enderror">
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            NIK dipakai sebagai identitas unik dan tidak boleh sama antar pengguna.
                        </p>
                        @error('nik')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                            Alamat Surel <span class="text-[#C8102E]">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ $nilai('email') }}"
                               maxlength="255" placeholder="nama@contoh.com"
                               class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('email') border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E] @else border-slate-300 focus:border-[#14346B] focus:ring-[#14346B] @enderror">
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Surel ini dipakai pengguna untuk masuk ke portal.
                        </p>
                        @error('email')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Kata sandi -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kata Sandi</h2>
                </div>

                <div class="px-6 py-6 lg:px-7 space-y-6">
                    @if (isset($user))
                        <div class="flex items-start gap-3 rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600 leading-relaxed">
                            <i class="fas fa-circle-info mt-0.5 text-slate-400"></i>
                            <span>Kosongkan kedua kolom di bawah bila kata sandi tidak ingin diubah.</span>
                        </div>
                    @endif

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                            {{ isset($user) ? 'Kata Sandi Baru' : 'Kata Sandi' }}
                            @unless (isset($user)) <span class="text-[#C8102E]">*</span> @endunless
                        </label>
                        <input type="password" id="password" name="password" autocomplete="new-password"
                               placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('password') border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E] @else border-slate-300 focus:border-[#14346B] focus:ring-[#14346B] @enderror">
                        @error('password')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">
                            Konfirmasi Kata Sandi
                            @unless (isset($user)) <span class="text-[#C8102E]">*</span> @endunless
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                               placeholder="Ulangi kata sandi"
                               class="w-full px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PENGATURAN ================= -->
        <aside class="lg:col-span-4 space-y-6">

            <!-- Peran -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="bg-[#14346B] px-5 py-4">
                    <h2 class="text-white font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-user-shield text-xs opacity-80"></i> Peran Akun
                    </h2>
                    <p class="text-blue-100/80 text-xs mt-1">Menentukan hak akses pengguna</p>
                </div>

                <div class="px-5 py-5">
                    @php $rolePilihan = $nilai('role', 'user'); @endphp
                    <div class="space-y-2">
                        @foreach ($roleList as $role)
                            <label class="flex items-start gap-3 px-4 py-3 rounded-md border transition-colors {{ $akunSendiri ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }} {{ $rolePilihan === $role ? 'border-[#14346B] bg-[#14346B]/5' : 'border-slate-300 hover:border-slate-400' }}">
                                <input type="radio" name="role" value="{{ $role }}"
                                       @checked($rolePilihan === $role)
                                       @disabled($akunSendiri)
                                       class="mt-0.5 accent-[#14346B]">
                                <span>
                                    <span class="block text-sm font-semibold text-slate-800">
                                        {{ \App\Models\User::ROLE_LABEL[$role] }}
                                    </span>
                                    <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed">
                                        {{ \App\Models\User::ROLE_KETERANGAN[$role] }}
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    @if ($akunSendiri)
                        {{-- Peran akun sendiri dikunci agar admin tidak mengeluarkan dirinya dari panel. --}}
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="mt-3 text-xs text-slate-500 leading-relaxed">
                            <i class="fas fa-lock mr-1"></i>
                            Peran akun Anda sendiri tidak dapat diubah dari halaman ini.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Informasi akun (hanya saat ubah) -->
            @if (isset($user))
                <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Informasi Akun</h2>
                    </div>
                    <dl class="px-5 py-5 space-y-4 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Terdaftar</dt>
                            <dd class="font-semibold text-slate-800 text-right">
                                {{ $user->created_at->translatedFormat('d F Y') }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Diperbarui</dt>
                            <dd class="font-semibold text-slate-800 text-right">
                                {{ $user->updated_at->translatedFormat('d F Y') }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">Total Pengaduan</dt>
                            <dd class="font-semibold text-slate-800 text-right tabular-nums">
                                {{ $user->pengaduans_count ?? $user->pengaduans()->count() }}
                            </dd>
                        </div>
                    </dl>
                </div>
            @endif

            <!-- Aksi -->
            <div class="border border-slate-200 rounded-lg bg-white px-5 py-5 space-y-3">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-[#14346B] text-white py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                    <i class="fas fa-floppy-disk text-xs"></i> {{ $labelTombol }}
                </button>
                <a href="{{ route('admin.user.index') }}"
                   class="w-full inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                    Batal
                </a>
            </div>
        </aside>
    </div>
</form>
