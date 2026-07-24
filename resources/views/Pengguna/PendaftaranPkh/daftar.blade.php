@extends('layouts.layout')
@section('title', 'Pendaftaran PKH - Portal PKH Dinas Sosial Kabupaten Mukomuko')
@section('content')
<style>
    .reveal { opacity: 0; transform: translateY(12px); transition: opacity .5s ease-out, transform .5s ease-out; }
    .reveal.is-visible { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) { .reveal { opacity: 1 !important; transform: none !important; transition: none !important; } }
</style>
<noscript><style>.reveal { opacity: 1; transform: none; }</style></noscript>

@php
    $inputBase = 'w-full border rounded-md px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition';
    $inputOk   = 'border-slate-300 focus:border-[#14346B] focus:ring-[#14346B]';
    $inputErr  = 'border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E]';
    $aktif     = $existing && in_array($existing->status, ['Baru', 'Diverifikasi'], true);
@endphp

    <!-- ================= JUDUL HALAMAN ================= -->
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <nav aria-label="Breadcrumb" class="text-xs text-slate-500">
                <ol class="flex items-center gap-2">
                    <li><a href="{{ url('/') }}" class="hover:text-[#14346B] transition-colors">Beranda</a></li>
                    <li class="text-slate-300">/</li>
                    <li class="text-slate-700 font-medium">Pendaftaran PKH</li>
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
                    Pendaftaran Calon Penerima
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-[#0E2650] leading-[1.2] tracking-tight">
                Pendaftaran PKH
            </h1>
            <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>
            <p class="mt-6 text-base text-slate-600 leading-relaxed max-w-3xl">
                Daftarkan diri Anda sebagai calon penerima Program Keluarga Harapan (PKH). Pengajuan akan ditinjau
                petugas Dinas Sosial Kabupaten Mukomuko dan dinilai secara objektif menggunakan metode
                <span class="font-semibold text-slate-800">Simple Additive Weighting (SAW)</span>.
            </p>
        </div>
    </section>

    <!-- ================= ISI ================= -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            @if (session('status') === 'pkh-terdaftar')
                <div class="mb-6 border border-emerald-200 bg-emerald-50 rounded-md px-5 py-4 flex items-start gap-3">
                    <i class="fas fa-circle-check text-emerald-600 mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-emerald-800 text-sm">Pendaftaran berhasil dikirim.</p>
                        <p class="text-sm text-emerald-700 mt-0.5">Pengajuan Anda tercatat dengan status <strong>Baru</strong> dan akan ditinjau petugas.</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 border border-rose-200 bg-rose-50 rounded-md px-5 py-4 flex items-start gap-3">
                    <i class="fas fa-circle-exclamation text-[#C8102E] mt-0.5"></i>
                    <p class="text-sm text-[#C8102E] font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if ($aktif)
                {{-- Sudah punya pengajuan berjalan: tampilkan status, sembunyikan formulir --}}
                <div class="max-w-2xl border border-slate-200 rounded-lg bg-white overflow-hidden">
                    <div class="px-7 py-6 border-b border-slate-200 flex items-center gap-4">
                        <span class="w-12 h-12 rounded-md flex items-center justify-center flex-shrink-0 {{ $existing->status === 'Diverifikasi' ? 'bg-emerald-50 text-emerald-600' : 'bg-[#14346B]/5 text-[#14346B]' }}">
                            <i class="fas {{ $existing->status === 'Diverifikasi' ? 'fa-user-check' : 'fa-hourglass-half' }} text-lg"></i>
                        </span>
                        <div>
                            <h2 class="font-bold text-[#0E2650] text-lg">
                                {{ $existing->status === 'Diverifikasi' ? 'Pengajuan Diverifikasi' : 'Pengajuan Sedang Ditinjau' }}
                            </h2>
                            <span class="inline-flex items-center mt-1 text-[11px] font-semibold px-3 py-1 rounded-md border {{ $existing->badgeStatus() }}">
                                {{ $existing->status }}
                            </span>
                        </div>
                    </div>
                    <div class="px-7 py-6">
                        <p class="text-sm text-slate-600 leading-relaxed">
                            @if ($existing->status === 'Diverifikasi')
                                Selamat, pengajuan Anda telah diverifikasi. Anda kini terdaftar sebagai calon penerima dan akan dinilai lebih lanjut oleh petugas.
                            @else
                                Pengajuan Anda telah kami terima pada {{ $existing->created_at->translatedFormat('d F Y, H:i') }} WIB dan sedang ditinjau petugas Dinas Sosial. Anda akan dihubungi bila diperlukan verifikasi lanjutan.
                            @endif
                        </p>
                        <a href="{{ url('/') }}" class="mt-6 inline-flex items-center gap-2 border border-slate-300 text-slate-700 px-6 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                            <i class="fas fa-arrow-left text-xs"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            @else
                @if ($errors->any())
                    <div class="mb-6 border border-rose-200 bg-rose-50 rounded-md px-5 py-4">
                        <p class="text-sm text-[#C8102E] font-semibold flex items-start gap-2 mb-1">
                            <i class="fas fa-circle-exclamation mt-0.5"></i>
                            <span>Pendaftaran belum dapat dikirim. Periksa kembali isian berikut:</span>
                        </p>
                        <ul class="text-sm text-[#C8102E]/90 list-disc pl-9 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($existing && $existing->status === 'Ditolak')
                    <div class="mb-6 border border-amber-200 bg-amber-50 rounded-md px-5 py-4">
                        <p class="text-sm text-amber-800 font-semibold"><i class="fas fa-circle-info mr-1.5"></i> Pengajuan sebelumnya ditolak.</p>
                        @if ($existing->catatan_admin)
                            <p class="text-sm text-amber-700 mt-1">Catatan petugas: {{ $existing->catatan_admin }}</p>
                        @endif
                        <p class="text-sm text-amber-700 mt-1">Anda dapat memperbaiki data dan mengajukan kembali.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('pkh.daftar.store') }}" enctype="multipart/form-data"
                      class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    @csrf

                    <div class="lg:col-span-8 space-y-6">

                        <!-- Bagian 01: Data diri -->
                        <div class="reveal bg-white border border-slate-200 rounded-lg">
                            <div class="flex items-center gap-3 px-7 py-5 border-b border-slate-200">
                                <span class="w-9 h-9 flex items-center justify-center rounded-md border border-[#14346B]/20 text-[#14346B] font-bold text-sm flex-shrink-0">01</span>
                                <div>
                                    <h2 class="font-semibold text-slate-900">Data Diri</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Isi sesuai dokumen kependudukan (KTP/KK).</p>
                                </div>
                            </div>

                            <div class="px-7 py-6 space-y-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label for="nama" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap <span class="text-[#C8102E]">*</span></label>
                                        <input type="text" id="nama" name="nama" required autocomplete="name"
                                               value="{{ old('nama', Auth::user()->name) }}" placeholder="Nama sesuai KTP"
                                               class="{{ $inputBase }} {{ $errors->has('nama') ? $inputErr : $inputOk }}">
                                        @error('nama') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="nik" class="block text-sm font-semibold text-slate-700 mb-2">NIK <span class="text-[#C8102E]">*</span></label>
                                        <input type="text" id="nik" name="nik" required inputmode="numeric" maxlength="16"
                                               value="{{ old('nik', Auth::user()->nik) }}" placeholder="16 digit NIK"
                                               class="{{ $inputBase }} tabular-nums {{ $errors->has('nik') ? $inputErr : $inputOk }}">
                                        @error('nik') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="desa" class="block text-sm font-semibold text-slate-700 mb-2">Desa/Kelurahan <span class="text-[#C8102E]">*</span></label>
                                    <select id="desa" name="desa" required
                                            class="{{ $inputBase }} {{ $errors->has('desa') ? $inputErr : $inputOk }}">
                                        <option value="">— Pilih desa —</option>
                                        @foreach (\App\Models\Pendaftaran::DESA as $desaOpsi)
                                            <option value="{{ $desaOpsi }}" @selected(old('desa') === $desaOpsi)>{{ $desaOpsi }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-slate-400 mt-1.5">Kecamatan Teramang Jaya, Kabupaten Mukomuko.</p>
                                    @error('desa') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="alamat" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Lengkap (Dusun/RT/RW) <span class="text-[#C8102E]">*</span></label>
                                    <textarea id="alamat" name="alamat" rows="3" required
                                              placeholder="Contoh: Dusun II, RT 003 / RW 001"
                                              class="{{ $inputBase }} resize-y {{ $errors->has('alamat') ? $inputErr : $inputOk }}">{{ old('alamat') }}</textarea>
                                    @error('alamat') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="no_hp" class="block text-sm font-semibold text-slate-700 mb-2">Nomor Telepon / WhatsApp <span class="text-slate-400 font-normal">(opsional)</span></label>
                                    <input type="text" id="no_hp" name="no_hp" inputmode="tel"
                                           value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890"
                                           class="{{ $inputBase }} {{ $errors->has('no_hp') ? $inputErr : $inputOk }}">
                                    @error('no_hp') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <span class="block text-sm font-semibold text-slate-700 mb-2">Foto Diri Memegang KTP <span class="text-[#C8102E]">*</span></span>
                                    <label for="foto_ktp"
                                           class="flex flex-col items-center justify-center gap-2 border border-dashed rounded-md p-6 text-center cursor-pointer hover:border-[#14346B] hover:bg-slate-50 transition-colors {{ $errors->has('foto_ktp') ? 'border-rose-300' : 'border-slate-300' }}">
                                        <span class="w-10 h-10 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center"><i class="fas fa-id-card text-sm"></i></span>
                                        <span class="text-sm text-slate-600"><span class="font-semibold text-[#14346B]">Pilih foto</span> diri sambil memegang KTP</span>
                                        <span class="text-[11px] text-slate-400">Wajah dan data KTP harus terlihat jelas &middot; JPG/PNG &middot; maks 5 MB</span>
                                        <span class="file-name text-[11px] font-semibold text-[#14346B] break-all"></span>
                                        <input id="foto_ktp" type="file" name="foto_ktp" accept=".jpg,.jpeg,.png" class="hidden">
                                    </label>
                                    @error('foto_ktp') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 02: Kondisi ekonomi -->
                        <div class="reveal bg-white border border-slate-200 rounded-lg" style="transition-delay:80ms">
                            <div class="flex items-center gap-3 px-7 py-5 border-b border-slate-200">
                                <span class="w-9 h-9 flex items-center justify-center rounded-md border border-[#14346B]/20 text-[#14346B] font-bold text-sm flex-shrink-0">02</span>
                                <div>
                                    <h2 class="font-semibold text-slate-900">Kondisi Ekonomi Keluarga</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Data ini menjadi dasar penilaian kelayakan (metode SAW).</p>
                                </div>
                            </div>

                            <div class="px-7 py-6 space-y-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label for="penghasilan" class="block text-sm font-semibold text-slate-700 mb-2">Penghasilan per Bulan <span class="text-[#C8102E]">*</span></label>
                                        <select id="penghasilan" name="penghasilan" required
                                                class="{{ $inputBase }} {{ $errors->has('penghasilan') ? $inputErr : $inputOk }}">
                                            <option value="">— Pilih —</option>
                                            @foreach ($penghasilanList as $opsi)
                                                <option value="{{ $opsi }}" @selected(old('penghasilan') === $opsi)>{{ $opsi }}</option>
                                            @endforeach
                                        </select>
                                        @error('penghasilan') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="jumlah_tanggungan" class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Tanggungan <span class="text-[#C8102E]">*</span></label>
                                        <input type="number" id="jumlah_tanggungan" name="jumlah_tanggungan" required min="0" max="30"
                                               value="{{ old('jumlah_tanggungan') }}" placeholder="mis. 3"
                                               class="{{ $inputBase }} {{ $errors->has('jumlah_tanggungan') ? $inputErr : $inputOk }}">
                                        @error('jumlah_tanggungan') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="kondisi_rumah" class="block text-sm font-semibold text-slate-700 mb-2">Kondisi Rumah <span class="text-[#C8102E]">*</span></label>
                                    <select id="kondisi_rumah" name="kondisi_rumah" required
                                            class="{{ $inputBase }} {{ $errors->has('kondisi_rumah') ? $inputErr : $inputOk }}">
                                        <option value="">— Pilih —</option>
                                        @foreach ($kondisiRumahList as $opsi)
                                            <option value="{{ $opsi }}" @selected(old('kondisi_rumah') === $opsi)>{{ $opsi }}</option>
                                        @endforeach
                                    </select>
                                    @error('kondisi_rumah') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="status_pekerjaan" class="block text-sm font-semibold text-slate-700 mb-2">Status Pekerjaan <span class="text-[#C8102E]">*</span></label>
                                    <select id="status_pekerjaan" name="status_pekerjaan" required
                                            class="{{ $inputBase }} {{ $errors->has('status_pekerjaan') ? $inputErr : $inputOk }}">
                                        <option value="">— Pilih —</option>
                                        @foreach ($statusPekerjaanList as $opsi)
                                            <option value="{{ $opsi }}" @selected(old('status_pekerjaan') === $opsi)>{{ $opsi }}</option>
                                        @endforeach
                                    </select>
                                    @error('status_pekerjaan') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="kepemilikan_aset" class="block text-sm font-semibold text-slate-700 mb-2">Kepemilikan Aset <span class="text-[#C8102E]">*</span></label>
                                    <select id="kepemilikan_aset" name="kepemilikan_aset" required
                                            class="{{ $inputBase }} {{ $errors->has('kepemilikan_aset') ? $inputErr : $inputOk }}">
                                        <option value="">— Pilih —</option>
                                        @foreach ($kepemilikanAsetList as $opsi)
                                            <option value="{{ $opsi }}" @selected(old('kepemilikan_aset') === $opsi)>{{ $opsi }}</option>
                                        @endforeach
                                    </select>
                                    @error('kepemilikan_aset') <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Bagian 03: Bukti kondisi rumah -->
                        <div class="reveal bg-white border border-slate-200 rounded-lg" style="transition-delay:120ms">
                            <div class="flex items-center gap-3 px-7 py-5 border-b border-slate-200">
                                <span class="w-9 h-9 flex items-center justify-center rounded-md border border-[#14346B]/20 text-[#14346B] font-bold text-sm flex-shrink-0">03</span>
                                <div>
                                    <h2 class="font-semibold text-slate-900">Bukti Kondisi Rumah</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Unggah minimal 4 foto: tampak depan, tampak belakang, ruang tamu, dan WC.</p>
                                </div>
                            </div>

                            <div class="px-7 py-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    @foreach (\App\Models\Pendaftaran::FOTO_RUMAH as $field => $label)
                                        <div>
                                            <span class="block text-sm font-semibold text-slate-700 mb-2">{{ $label }} <span class="text-[#C8102E]">*</span></span>
                                            <label for="{{ $field }}"
                                                   class="flex flex-col items-center justify-center gap-2 border border-dashed rounded-md p-5 text-center cursor-pointer hover:border-[#14346B] hover:bg-slate-50 transition-colors {{ $errors->has($field) ? 'border-rose-300' : 'border-slate-300' }}">
                                                <span class="w-9 h-9 rounded-md bg-[#14346B]/5 text-[#14346B] flex items-center justify-center"><i class="fas fa-camera text-sm"></i></span>
                                                <span class="text-xs text-slate-600"><span class="font-semibold text-[#14346B]">Pilih foto</span> dari perangkat</span>
                                                <span class="text-[11px] text-slate-400">JPG atau PNG &middot; maks 5 MB</span>
                                                <span class="file-name text-[11px] font-semibold text-[#14346B] break-all"></span>
                                                <input id="{{ $field }}" type="file" name="{{ $field }}" accept=".jpg,.jpeg,.png" class="hidden">
                                            </label>
                                            @error($field) <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p> @enderror
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-5 flex items-start gap-2 text-xs text-slate-500 leading-relaxed border-t border-slate-100 pt-4">
                                    <i class="fas fa-circle-info text-slate-400 mt-0.5"></i>
                                    Foto dipakai petugas untuk memverifikasi kelayakan hunian dan bersifat rahasia.
                                </p>
                            </div>

                            <div class="px-7 py-5 border-t border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center gap-3">
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 bg-[#14346B] text-white px-6 py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                                    <i class="fas fa-paper-plane text-xs"></i> Kirim Pendaftaran
                                </button>
                                <p class="text-xs text-slate-500 sm:ml-auto">Tanda <span class="text-[#C8102E]">*</span> wajib diisi.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Panel keterangan -->
                    <aside class="lg:col-span-4 space-y-6">
                        <div class="reveal bg-white border border-slate-200 rounded-lg overflow-hidden" style="transition-delay:40ms">
                            <div class="bg-[#14346B] px-6 py-4">
                                <h2 class="text-white font-semibold text-base flex items-center gap-2">
                                    <i class="fas fa-circle-info text-sm opacity-80"></i> Ketentuan Pendaftaran
                                </h2>
                            </div>
                            <ul class="divide-y divide-slate-100 text-sm text-slate-600">
                                @php
                                    $ketentuan = [
                                        'Isi data dengan benar dan dapat dipertanggungjawabkan.',
                                        'Satu warga cukup mengajukan satu kali; pengajuan aktif tidak dapat digandakan.',
                                        'Pengajuan ditinjau petugas sebelum masuk penilaian SAW.',
                                        'Pendaftaran tidak menjamin diterima; keputusan berdasarkan hasil penilaian.',
                                    ];
                                @endphp
                                @foreach($ketentuan as $index => $poin)
                                <li class="flex items-start gap-3 px-6 py-4 leading-relaxed">
                                    <span class="text-xs font-bold text-[#14346B]/50 mt-0.5 flex-shrink-0">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    {{ $poin }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </aside>
                </form>
            @endif
        </div>
    </section>

    <script>
        (function () {
            var els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) {
                els.forEach(function (el) { el.classList.add('is-visible'); });
            } else {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) { entry.target.classList.add('is-visible'); io.unobserve(entry.target); }
                    });
                }, { threshold: 0.1 });
                els.forEach(function (el) { io.observe(el); });
            }

            // Tampilkan nama berkas foto yang dipilih.
            document.querySelectorAll('input[type=file]').forEach(function (inp) {
                inp.addEventListener('change', function () {
                    var span = inp.parentElement.querySelector('.file-name');
                    if (span) span.textContent = inp.files.length ? inp.files[0].name : '';
                });
            });
        })();
    </script>
@endsection
