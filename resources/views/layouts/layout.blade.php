<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal PKH - Transparan & Tepat Sasaran')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>[x-cloak]{ display: none !important; }</style>
</head>
<body class="bg-white font-sans flex flex-col min-h-screen text-slate-800">
    <!-- GARIS IDENTITAS MERAH PUTIH -->
    <div class="h-1 bg-[#C8102E]"></div>

    <!-- TOP BAR IDENTITAS PEMERINTAH -->
    <div class="bg-[#0E2650] text-blue-100/80 text-xs hidden sm:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-9">
            <span class="font-medium tracking-wide">
                PEMERINTAH KABUPATEN MUKOMUKO &middot; PROVINSI BENGKULU
            </span>
            <span class="hidden md:flex items-center gap-5">
                <a href="mailto:dinassosialkabmukomuko@gmail.com" class="hover:text-white transition-colors">
                    <i class="fas fa-envelope mr-1.5 opacity-70"></i> dinassosialkabmukomuko@gmail.com
                </a>
                <span class="text-blue-100/40">|</span>
                <span><i class="fas fa-clock mr-1.5 opacity-70"></i> Senin &ndash; Jumat, 08.00 WIB</span>
            </span>
        </div>
    </div>

    <!-- NAVBAR WITH DYNAMIC ACTIVE STATES -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-[72px] items-center">

                <!-- GRUP KIRI: Logo & Menu Desktop -->
                <div class="flex items-center gap-8 lg:gap-10">
                    <!-- Logo & Nama Instansi -->
                    <a href="/" class="flex items-center gap-3">
                        <img src="{{ asset('assets/img/Logo_Mukomuko.png') }}" alt="Logo Kabupaten Mukomuko" class="w-10 h-10 object-contain flex-shrink-0">
                        <span class="flex flex-col leading-tight border-l border-slate-200 pl-3">
                            <span class="text-[15px] font-bold text-[#0E2650] tracking-tight">DINAS SOSIAL</span>
                            <span class="text-[11px] font-medium text-slate-500 tracking-[0.08em] uppercase">Kabupaten Mukomuko</span>
                        </span>
                    </a>

                    <!-- Desktop Menu dengan Deteksi Rute Otomatis -->
                    @php
                        $nav_links = [
                            ['label' => 'Beranda',   'url' => url('/'),            'active' => request()->is('/')],
                            ['label' => 'Daftar PKH','url' => route('pkh.daftar'),  'active' => request()->routeIs('pkh.daftar')],
                            ['label' => 'Pengaduan', 'url' => route('pengaduan'),   'active' => request()->routeIs('pengaduan')],
                            ['label' => 'Berita',    'url' => route('berita.index'),'active' => request()->routeIs('berita.*')],
                            ['label' => 'Galeri',    'url' => route('galeri.index'),'active' => request()->routeIs('galeri.*')],
                            ['label' => 'Tentang',   'url' => route('tentang'),     'active' => request()->routeIs('tentang')],
                        ];
                    @endphp
                    <div class="hidden md:flex items-center gap-1">
                        @foreach($nav_links as $link)
                        <a href="{{ $link['url'] }}"
                           class="relative px-3.5 py-2 text-sm transition-colors {{ $link['active'] ? 'text-[#14346B] font-semibold' : 'text-slate-600 font-medium hover:text-[#14346B]' }}">
                            {{ $link['label'] }}
                            <span class="absolute -bottom-[18px] left-0 w-full h-0.5 bg-[#14346B] {{ $link['active'] ? '' : 'hidden' }}"></span>
                        </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- GRUP KANAN -->
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-3">
                        @guest
                            <!-- Belum login: Daftar + Masuk -->
                            <a href="{{ route('register') }}" class="text-sm text-slate-700 font-semibold px-5 py-2.5 rounded-md border border-slate-300 hover:border-[#14346B] hover:text-[#14346B] transition-colors inline-block">
                                Daftar
                            </a>
                            <a href="{{ route('login') }}" class="text-sm bg-[#14346B] text-white px-5 py-2.5 rounded-md font-semibold hover:bg-[#0E2650] transition-colors inline-block">
                                <i class="fas fa-right-to-bracket mr-2 text-xs"></i> Masuk
                            </a>
                        @else
                            <!-- Sudah login: nama pengguna + dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2.5 border border-slate-300 hover:border-[#14346B] text-slate-800 pl-1.5 pr-3.5 py-1.5 rounded-md text-sm font-semibold transition-colors">
                                    <span class="w-7 h-7 rounded-md bg-[#14346B] text-white flex items-center justify-center text-xs flex-shrink-0">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                    <span class="max-w-[140px] truncate">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                </button>
                                <div x-show="open" x-cloak @click.outside="open = false"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="absolute right-0 mt-2 w-60 bg-white rounded-md shadow-lg border border-slate-200 py-1.5 z-50">
                                    @if (Route::has('profile'))
                                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                                        <i class="fas fa-user text-[#14346B] w-4 text-center"></i> Profil
                                    </a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition">
                                            <i class="fas fa-sign-out-alt w-4 text-center"></i> Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                    <!-- Mobile Menu Button -->
                    <div class="md:hidden flex items-center">
                        <button id="mobile-menu-btn" class="text-slate-600 hover:text-[#14346B] focus:outline-none p-2 border border-slate-300 rounded-md">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-200 absolute w-full shadow-md">
            <div class="px-4 py-3">
                <div class="divide-y divide-slate-100">
                    @foreach($nav_links as $link)
                    <a href="{{ $link['url'] }}" class="block px-2 py-3 text-[15px] {{ $link['active'] ? 'text-[#14346B] font-semibold' : 'text-slate-700 font-medium' }}">{{ $link['label'] }}</a>
                    @endforeach
                </div>
                <div class="border-t border-slate-200 pt-4 mt-3 pb-2">
                    @guest
                        <a href="{{ route('register') }}" class="block w-full text-center border border-slate-300 text-slate-700 px-5 py-2.5 rounded-md text-sm font-semibold mb-2.5">Daftar</a>
                        <a href="{{ route('login') }}" class="block w-full text-center bg-[#14346B] text-white px-5 py-2.5 rounded-md text-sm font-semibold">Masuk</a>
                    @else
                        @if (Route::has('profile'))
                        <a href="{{ route('profile') }}" class="block px-2 py-3 text-[15px] font-medium text-slate-700">Profil</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-2 py-3 text-[15px] font-medium text-[#C8102E]">Keluar</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
    <!-- AKHIR NAVBAR -->
    <!-- Konten Dinamis -->
    <main class="flex-grow">
        @yield('content')
    </main>
    <!-- FOOTER INSTANSI -->
    <footer class="bg-[#0E2650] text-blue-100/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-12 gap-10">
            <div class="md:col-span-5">
                <div class="flex items-start gap-3 mb-5">
                    <img src="{{ asset('assets/img/Logo_Mukomuko.png') }}" alt="Logo Kabupaten Mukomuko" class="w-11 h-11 object-contain flex-shrink-0">
                    <span class="flex flex-col leading-tight">
                        <span class="text-white font-bold tracking-tight">DINAS SOSIAL</span>
                        <span class="text-xs tracking-[0.08em] uppercase">Kabupaten Mukomuko</span>
                    </span>
                </div>
                <p class="text-sm leading-relaxed mb-5">
                    Portal resmi Program Keluarga Harapan (PKH) untuk penetapan dan pengecekan status penerima
                    bantuan sosial secara objektif dan transparan.
                </p>
                <ul class="text-sm space-y-2.5">
                    <li class="flex gap-3">
                        <i class="fas fa-location-dot mt-1 text-blue-300/60 w-4 text-center flex-shrink-0"></i>
                        <span>Jl. Imam Bonjol, Komplek Perkantoran Pemda, Kel. Bandar Ratu, Kec. Kota Mukomuko, Bengkulu 38714</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fas fa-envelope mt-1 text-blue-300/60 w-4 text-center flex-shrink-0"></i>
                        <a href="mailto:dinassosialkabmukomuko@gmail.com" class="hover:text-white transition-colors break-all">dinassosialkabmukomuko@gmail.com</a>
                    </li>
                </ul>
            </div>

            <div class="md:col-span-3">
                <h4 class="text-white font-semibold text-sm uppercase tracking-[0.12em] mb-5">Navigasi</h4>
                <ul class="text-sm space-y-3">
                    <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="{{ route('pkh.daftar') }}" class="hover:text-white transition-colors">Pendaftaran PKH</a></li>
                    <li><a href="{{ route('pengaduan') }}" class="hover:text-white transition-colors">Pengaduan Masyarakat</a></li>
                    <li><a href="{{ route('tentang') }}" class="hover:text-white transition-colors">Profil Dinas</a></li>
                </ul>
            </div>

            <div class="md:col-span-4">
                <h4 class="text-white font-semibold text-sm uppercase tracking-[0.12em] mb-5">Tautan Terkait</h4>
                <ul class="text-sm space-y-3">
                    <li><a href="https://kemensos.go.id" target="_blank" rel="noopener" class="hover:text-white transition-colors">Kementerian Sosial RI</a></li>
                    <li><a href="https://cekbansos.kemensos.go.id" target="_blank" rel="noopener" class="hover:text-white transition-colors">Cek Status PKH Kemensos</a></li>
                    <li><a href="https://www.lapor.go.id" target="_blank" rel="noopener" class="hover:text-white transition-colors">Layanan Aspirasi (LAPOR!)</a></li>
                    <li><a href="https://mukomukokab.go.id" target="_blank" rel="noopener" class="hover:text-white transition-colors">Pemkab Mukomuko</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 text-xs text-blue-100/50 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} Dinas Sosial Kabupaten Mukomuko. Seluruh hak cipta dilindungi.</p>
                <p>Pemerintah Kabupaten Mukomuko &middot; Provinsi Bengkulu</p>
            </div>
        </div>
    </footer>
    <!-- Script minimalis hanya untuk menu mobile -->
    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>