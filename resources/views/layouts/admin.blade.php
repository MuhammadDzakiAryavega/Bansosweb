<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Portal PKH')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        [x-cloak]{ display: none !important; }

        /* Animasi dibuat seminimal mungkin: hanya fade-in halus, selaras sisi pengguna */
        .reveal {
            opacity: 0;
            transform: translateY(12px);
            transition: opacity .5s ease-out, transform .5s ease-out;
        }
        .reveal.is-visible { opacity: 1; transform: none; }

        .side-link { transition: background-color .2s ease, color .2s ease, border-color .2s ease; }

        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
            .side-link { transition: none !important; }
        }
    </style>
    <noscript><style>.reveal { opacity: 1; transform: none; }</style></noscript>
    @stack('styles')
</head>
<body class="bg-slate-50 font-sans min-h-screen text-slate-800" x-data="{ sidebar: false }">

{{-- Menu sidebar: satu sumber, dipakai versi desktop & mobile --}}
@php
    // Sub-menu "Kelola PKH" dibangun dari daftar kriteria pada controller agar satu sumber.
    $pkhChildren = collect(\App\Http\Controllers\Admin\PkhController::KRITERIA)
        ->map(fn ($k, $slug) => [
            'label'  => $k['label'],
            'icon'   => $k['icon'],
            'url'    => route('admin.pkh.kriteria', $slug),
            'active' => request()->routeIs('admin.pkh.kriteria') && request()->route('kriteria') === $slug,
        ])
        ->values()
        ->all();

    // Pendaftaran masuk, penilaian calon, & hasil akhir setelah kriteria terakhir.
    $pkhChildren[] = [
        'label'  => 'Pendaftaran Masuk',
        'icon'   => 'fa-inbox',
        'url'    => route('admin.pkh.pendaftaran.index'),
        'active' => request()->routeIs('admin.pkh.pendaftaran.*'),
    ];
    $pkhChildren[] = [
        'label'  => 'Penilaian Calon',
        'icon'   => 'fa-clipboard-check',
        'url'    => route('admin.pkh.penilaian.index'),
        'active' => request()->routeIs('admin.pkh.penilaian.*'),
    ];
    $pkhChildren[] = [
        'label'  => 'Hasil Akhir',
        'icon'   => 'fa-trophy',
        'url'    => route('admin.pkh.hasil'),
        'active' => request()->routeIs('admin.pkh.hasil'),
    ];

    $menu = [
        [
            'label'  => 'Dashboard',
            'icon'   => 'fa-gauge-high',
            'url'    => route('dashboard'),
            'active' => request()->routeIs('dashboard'),
        ],
        [
            'label'    => 'Kelola PKH',
            'icon'     => 'fa-clipboard-list',
            'active'   => request()->routeIs('admin.pkh.*'),
            'children' => $pkhChildren,
        ],
        [
            'label'  => 'Kelola Pengaduan',
            'icon'   => 'fa-bullhorn',
            'url'    => route('admin.pengaduan.index'),
            'active' => request()->routeIs('admin.pengaduan.*'),
        ],
        [
            'label'  => 'Kelola Berita',
            'icon'   => 'fa-newspaper',
            'url'    => route('admin.berita.index'),
            'active' => request()->routeIs('admin.berita.*'),
        ],
        [
            'label'  => 'Kelola Galeri',
            'icon'   => 'fa-images',
            'url'    => route('admin.galeri.index'),
            'active' => request()->routeIs('admin.galeri.*'),
        ],
        [
            'label'  => 'Kelola Pengguna',
            'icon'   => 'fa-users',
            'url'    => route('admin.user.index'),
            'active' => request()->routeIs('admin.user.*'),
        ],
        [
            'label'  => 'Kelola Arsip',
            'icon'   => 'fa-folder-open',
            'url'    => route('admin.arsip.index'),
            'active' => request()->routeIs('admin.arsip.*'),
        ],
    ];
@endphp

<!-- GARIS IDENTITAS MERAH -->
<div class="h-1 bg-[#C8102E]"></div>

<div class="flex min-h-screen">

    <!-- ================= SIDEBAR (desktop) ================= -->
    <aside class="hidden lg:flex flex-col w-72 flex-shrink-0 bg-[#0E2650] text-white sticky top-0 self-start h-screen">
        @include('layouts.partials.admin-sidebar', ['menu' => $menu])
    </aside>

    <!-- ================= SIDEBAR (mobile drawer) ================= -->
    <div x-show="sidebar" x-cloak class="lg:hidden fixed inset-0 z-40">
        <div x-show="sidebar" x-transition.opacity
             @click="sidebar = false"
             class="absolute inset-0 bg-slate-900/60"></div>
        <aside x-show="sidebar"
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="relative flex flex-col w-72 h-full bg-[#0E2650] text-white">
            <button @click="sidebar = false" type="button"
                    class="absolute top-4 right-4 w-9 h-9 rounded-md border border-white/20 hover:bg-white/10 flex items-center justify-center transition-colors">
                <i class="fas fa-xmark"></i>
            </button>
            @include('layouts.partials.admin-sidebar', ['menu' => $menu])
        </aside>
    </div>

    <!-- ================= KONTEN ================= -->
    <div class="flex-1 min-w-0 flex flex-col">

        <!-- Bar identitas pemerintah -->
        <div class="bg-[#0E2650] text-blue-100/80 text-xs hidden lg:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-9">
                <span class="font-medium tracking-wide">
                    PEMERINTAH KABUPATEN MUKOMUKO &middot; PROVINSI BENGKULU
                </span>
                <span class="flex items-center gap-5">
                    <span><i class="fas fa-user-shield mr-1.5 opacity-70"></i> {{ Auth::user()->name }}</span>
                </span>
            </div>
        </div>

        <!-- Topbar mobile -->
        <div class="lg:hidden sticky top-0 z-30 bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
            <button @click="sidebar = true" type="button"
                    class="w-10 h-10 rounded-md border border-slate-300 text-slate-600 flex items-center justify-center hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                <i class="fas fa-bars"></i>
            </button>
            <span class="text-sm font-bold text-[#0E2650] tracking-tight">PANEL ADMIN</span>
            <span class="w-9 h-9 rounded-md bg-[#14346B] text-white flex items-center justify-center font-semibold text-xs">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </span>
        </div>

        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">

            <!-- Header halaman -->
            <div class="mb-8">
                <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
                    Administrasi Portal PKH
                </span>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0E2650] mt-2 tracking-tight">
                    @yield('page-title', 'Dashboard')
                </h1>
                <div class="w-12 h-1 bg-[#C8102E] mt-4"></div>
                @hasSection('page-subtitle')
                    <p class="text-slate-600 mt-4 text-sm md:text-base leading-relaxed max-w-3xl">@yield('page-subtitle')</p>
                @endif
            </div>

            @if (session('success'))
                <div class="mb-6 flex items-start gap-3 rounded-md border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 text-sm">
                    <i class="fas fa-circle-check mt-0.5"></i>
                    <span class="font-medium leading-relaxed">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-md border border-rose-200 bg-rose-50 text-rose-700 px-5 py-4 text-sm">
                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                    <span class="font-medium leading-relaxed">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')

        </main>

        <!-- Footer instansi -->
        <footer class="bg-[#0E2650] text-blue-100/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 text-xs flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} Dinas Sosial Kabupaten Mukomuko. Seluruh hak cipta dilindungi.</p>
                <p>Panel Administrator Portal PKH &middot; Kec. Teramang Jaya</p>
            </div>
        </footer>
    </div>
</div>

<script>
    (function () {
        // Fade-in halus saat elemen masuk viewport
        var els = document.querySelectorAll('.reveal');
        if (!('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('is-visible'); });
        } else {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            els.forEach(function (el) { io.observe(el); });
        }

        // Jam & tanggal server (WIB) + sapaan sesuai waktu
        function updateClock() {
            var now = new Date();
            var fmtTime = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
            });
            var fmtDate = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta', weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            });
            var clockEl = document.getElementById('clock');
            var dateEl = document.getElementById('date');
            if (clockEl) clockEl.textContent = fmtTime.format(now).replace(/\./g, ':');
            if (dateEl) dateEl.textContent = fmtDate.format(now);

            var greetingEl = document.getElementById('greeting');
            if (greetingEl) {
                var hour = parseInt(new Intl.DateTimeFormat('id-ID', {
                    timeZone: 'Asia/Jakarta', hour: '2-digit', hour12: false
                }).format(now), 10);
                var greeting = 'Selamat Malam';
                if (hour >= 4 && hour < 11) greeting = 'Selamat Pagi';
                else if (hour >= 11 && hour < 15) greeting = 'Selamat Siang';
                else if (hour >= 15 && hour < 18) greeting = 'Selamat Sore';
                greetingEl.textContent = greeting;
            }
        }
        updateClock();
        setInterval(updateClock, 1000);
    })();
</script>
@stack('scripts')
</body>
</html>
