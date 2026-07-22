<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Portal PKH Dinas Sosial Kabupaten Mukomuko</title>
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
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-blue-100/60">Portal PKH</span>
                <h1 class="text-3xl font-bold text-white mt-3 leading-snug">
                    Akses layanan<br>Program Keluarga Harapan
                </h1>
                <div class="w-16 h-1 bg-[#C8102E] mt-6"></div>
                <p class="text-blue-100/70 text-sm leading-relaxed mt-6 max-w-md">
                    Masuk untuk mengajukan pengaduan penyaluran bantuan, memantau tindak lanjutnya,
                    dan mengelola data akun Anda.
                </p>

                <ul class="mt-8 space-y-3 text-sm text-blue-100/80">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-bullhorn text-blue-200/50 mt-1 w-4 text-center flex-shrink-0"></i>
                        Ajukan pengaduan penyaluran PKH
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-clock-rotate-left text-blue-200/50 mt-1 w-4 text-center flex-shrink-0"></i>
                        Pantau status tindak lanjut laporan
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-user text-blue-200/50 mt-1 w-4 text-center flex-shrink-0"></i>
                        Kelola data profil kepesertaan
                    </li>
                </ul>
            </div>

            <p class="text-xs text-blue-100/40">
                &copy; {{ date('Y') }} Dinas Sosial Kabupaten Mukomuko &middot; Provinsi Bengkulu
            </p>
        </aside>

        <!-- ========== FORM ========== -->
        <main class="lg:col-span-7 flex items-center justify-center px-4 py-10 sm:py-14">
            <div class="w-full max-w-md">

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
                        <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Masuk Akun</span>
                        <h2 class="text-2xl font-bold text-[#0E2650] mt-2">Masuk ke Portal PKH</h2>
                        <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                        <p class="text-sm text-slate-500 leading-relaxed mt-4">
                            Gunakan surel dan kata sandi yang terdaftar pada portal ini.
                        </p>
                    </div>

                    <div class="px-7 sm:px-8 py-7">
                        @if ($errors->any())
                            <div class="mb-6 border border-rose-200 bg-rose-50 rounded-md px-4 py-3">
                                <p class="text-sm text-[#C8102E] font-semibold flex items-start gap-2">
                                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                                    <span>{{ $errors->first() }}</span>
                                </p>
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST" class="space-y-5">
                            @csrf

                            <div>
                                <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Surel</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                       placeholder="contoh@mail.com"
                                       class="w-full border rounded-md px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition {{ $errors->has('email') ? 'border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E]' : 'border-slate-300 focus:border-[#14346B] focus:ring-[#14346B]' }}">
                                @error('email')
                                    <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Kata Sandi</label>
                                <input type="password" id="password" name="password" required
                                       placeholder="Masukkan kata sandi"
                                       class="w-full border rounded-md px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition {{ $errors->has('password') ? 'border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E]' : 'border-slate-300 focus:border-[#14346B] focus:ring-[#14346B]' }}">
                                @error('password')
                                    <p class="text-xs text-[#C8102E] mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <label class="flex items-center gap-2.5 text-sm text-slate-600 select-none">
                                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 text-[#14346B] focus:ring-[#14346B]">
                                Ingat saya di perangkat ini
                            </label>

                            <button type="submit"
                                    class="w-full bg-[#14346B] text-white py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                                Masuk
                            </button>
                        </form>

                        <p class="text-sm text-slate-600 text-center mt-6 pt-6 border-t border-slate-100">
                            Belum memiliki akun?
                            <a href="{{ route('register') }}" class="font-semibold text-[#14346B] hover:underline">Daftar di sini</a>
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
</body>
</html>
