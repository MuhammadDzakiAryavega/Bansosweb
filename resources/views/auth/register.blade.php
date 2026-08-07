<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Portal PKH Dinas Sosial Kabupaten Mukomuko</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans min-h-screen flex flex-col text-slate-800">
    <div class="h-1 bg-[#C8102E]"></div>

    <div class="flex-grow grid grid-cols-1 lg:grid-cols-12">

        <!-- ========== PANEL IDENTITAS (desktop) ========== -->
        <aside class="hidden lg:flex lg:col-span-5 bg-[#0E2650] flex-col justify-between p-12">
            <div>
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/img/Logo_Mukomuko.png') }}" alt="Logo Kabupaten Mukomuko" class="w-11 h-11 object-contain flex-shrink-0">
                    <span class="flex flex-col leading-tight border-l border-white/20 pl-3">
                        <span class="text-white font-bold tracking-tight">DINAS SOSIAL</span>
                        <span class="text-[11px] text-blue-100/70 tracking-[0.08em] uppercase">Kabupaten Mukomuko</span>
                    </span>
                </a>
            </div>

            <div>
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-blue-100/60">Pendaftaran Akun</span>
                <h1 class="text-3xl font-bold text-white mt-3 leading-snug">
                    Satu akun untuk<br>seluruh layanan PKH
                </h1>
                <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>
                <p class="text-blue-100/70 text-sm leading-relaxed mt-6 max-w-md">
                    Daftarkan akun menggunakan data sesuai Kartu Tanda Penduduk. Data yang Anda isikan
                    digunakan untuk keperluan verifikasi penyaluran bantuan sosial.
                </p>

                <ul class="mt-8 space-y-3 text-sm text-blue-100/80">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-id-card text-blue-200/50 mt-1 w-4 text-center flex-shrink-0"></i>
                        NIK diisi sesuai KTP, terdiri dari 16 digit angka
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-envelope text-blue-200/50 mt-1 w-4 text-center flex-shrink-0"></i>
                        Surel aktif digunakan untuk masuk ke portal
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-lock text-blue-200/50 mt-1 w-4 text-center flex-shrink-0"></i>
                        Kata sandi minimal 8 karakter
                    </li>
                </ul>
            </div>

            <p class="text-xs text-blue-100/40">
                &copy; {{ date('Y') }} Dinas Sosial Kabupaten Mukomuko &middot; Provinsi Bengkulu
            </p>
        </aside>

        <!-- ========== FORM ========== -->
        <main class="lg:col-span-7 flex items-center justify-center px-4 py-10 sm:py-14">
            <div class="w-full max-w-xl">

                <!-- Identitas versi mobile -->
                <a href="{{ url('/') }}" class="lg:hidden flex items-center gap-3 justify-center mb-8">
                    <img src="{{ asset('assets/img/Logo_Mukomuko.png') }}" alt="Logo Kabupaten Mukomuko" class="w-10 h-10 object-contain flex-shrink-0">
                    <span class="flex flex-col leading-tight border-l border-slate-200 pl-3">
                        <span class="text-[15px] font-bold text-[#0E2650] tracking-tight">DINAS SOSIAL</span>
                        <span class="text-[11px] font-medium text-slate-500 tracking-[0.08em] uppercase">Kabupaten Mukomuko</span>
                    </span>
                </a>

                <div class="bg-white border border-slate-200 rounded-lg">
                    <div class="px-7 sm:px-8 pt-8 pb-6 border-b border-slate-100">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Daftar Akun</span>
                        <h2 class="text-2xl font-bold text-[#0E2650] mt-2">Pendaftaran Akun Baru</h2>
                        <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                        <p class="text-sm text-slate-500 leading-relaxed mt-4">
                            Lengkapi data berikut sesuai dokumen kependudukan Anda.
                        </p>
                    </div>

                    <div class="px-7 sm:px-8 py-7">
                        @if ($errors->any())
                            <div class="mb-6 border border-rose-200 bg-rose-50 rounded-md px-4 py-3">
                                <p class="text-sm text-[#C8102E] font-semibold flex items-start gap-2 mb-1">
                                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                                    <span>Periksa kembali isian berikut:</span>
                                </p>
                                <ul class="text-sm text-[#C8102E]/90 list-disc pl-9 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @php
                            $inputBase = 'w-full border rounded-md px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition';
                            $inputOk   = 'border-slate-300 focus:border-[#14346B] focus:ring-[#14346B]';
                            $inputErr  = 'border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E]';
                        @endphp

                        <form action="{{ route('register') }}" method="POST" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                                           placeholder="Nama sesuai KTP"
                                           class="{{ $inputBase }} {{ $errors->has('name') ? $inputErr : $inputOk }}">
                                    @error('name')
                                        <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nik" class="block text-sm font-semibold text-slate-700 mb-2">NIK</label>
                                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required
                                           inputmode="numeric" maxlength="16" placeholder="16 digit angka"
                                           class="{{ $inputBase }} {{ $errors->has('nik') ? $inputErr : $inputOk }}">
                                    @error('nik')
                                        <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Surel</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                       placeholder="contoh@mail.com"
                                       class="{{ $inputBase }} {{ $errors->has('email') ? $inputErr : $inputOk }}">
                                @error('email')
                                    <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Kata Sandi</label>
                                    <input type="password" id="password" name="password" required
                                           maxlength="20" placeholder="8 sampai 20 karakter"
                                           class="{{ $inputBase }} {{ $errors->has('password') ? $inputErr : $inputOk }}">
                                    @error('password')
                                        <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Kata Sandi</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required
                                           maxlength="20" placeholder="Ulangi kata sandi"
                                           class="{{ $inputBase }} {{ $inputOk }}">
                                </div>
                            </div>

                            <label class="flex items-start gap-3 text-sm text-slate-600 select-none border-t border-slate-100 pt-5">
                                <input type="checkbox" required
                                       class="mt-0.5 w-4 h-4 rounded border-slate-300 text-[#14346B] focus:ring-[#14346B] flex-shrink-0">
                                <span class="leading-relaxed">
                                    Saya menyatakan data yang diisikan benar dan bersedia data tersebut diverifikasi
                                    oleh Dinas Sosial Kabupaten Mukomuko untuk keperluan penyaluran bantuan sosial.
                                </span>
                            </label>

                            <button type="submit"
                                    class="w-full bg-[#14346B] text-white py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                                Daftar Akun
                            </button>
                        </form>

                        <p class="text-sm text-slate-600 text-center mt-6 pt-6 border-t border-slate-100">
                            Sudah memiliki akun?
                            <a href="{{ route('login') }}" class="font-semibold text-[#14346B] hover:underline">Masuk di sini</a>
                        </p>
                    </div>
                </div>

                <div class="text-center mt-6">
                    <a href="{{ url('/') }}" class="text-sm text-slate-500 hover:text-[#14346B] transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left text-[10px]"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // NIK hanya menerima angka, maksimal 16 digit
        (function () {
            var nik = document.getElementById('nik');
            if (!nik) return;
            nik.addEventListener('input', function () {
                nik.value = nik.value.replace(/\D/g, '').slice(0, 16);
            });
        })();
    </script>
</body>
</html>
