{{-- Isi sidebar admin. Dipakai bersama oleh sidebar desktop & drawer mobile. --}}

<!-- Identitas instansi -->
<div class="px-6 py-5 border-b border-white/10">
    <div class="flex items-center gap-3">
        <img src="{{ asset('assets/img/Logo_Mukomuko.png') }}" alt="Logo Kabupaten Mukomuko"
             class="w-10 h-10 object-contain flex-shrink-0 bg-white rounded-md p-1">
        <span class="flex flex-col leading-tight border-l border-white/20 pl-3">
            <span class="text-[15px] font-bold text-white tracking-tight">PANEL ADMIN</span>
            <span class="text-[11px] font-medium text-blue-100/60 tracking-[0.08em] uppercase">Dinas Sosial Mukomuko</span>
        </span>
    </div>
</div>

<!-- Menu -->
<nav class="flex-1 px-4 py-6 overflow-y-auto">
    <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-blue-100/40 mb-3">Menu Utama</p>
    <div class="space-y-1">
        @foreach ($menu as $item)
            @if (!empty($item['children']))
                {{-- Menu bertingkat (dropdown). Terbuka otomatis bila salah satu anak sedang aktif. --}}
                <div x-data="{ open: {{ $item['active'] ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                            class="side-link w-full flex items-center gap-3 px-3 py-2.5 rounded-md text-sm border-l-2 {{ $item['active'] ? 'bg-white/10 border-[#C8102E] text-white font-semibold' : 'border-transparent text-blue-100/70 font-medium hover:text-white hover:bg-white/5' }}">
                        <i class="fas {{ $item['icon'] }} w-4 text-center text-xs"></i>
                        <span class="flex-1 text-left">{{ $item['label'] }}</span>
                        <i class="fas fa-chevron-down text-[10px] transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-1 ml-4 pl-3 border-l border-white/10 space-y-1">
                        @foreach ($item['children'] as $child)
                            <a href="{{ $child['url'] }}"
                               class="side-link flex items-center gap-3 px-3 py-2 rounded-md text-[13px] {{ $child['active'] ? 'text-white font-semibold bg-white/5' : 'text-blue-100/60 font-medium hover:text-white hover:bg-white/5' }}">
                                <i class="fas {{ $child['icon'] }} w-4 text-center text-[11px] {{ $child['active'] ? 'text-[#C8102E]' : '' }}"></i> {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a href="{{ $item['url'] }}"
                   class="side-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm border-l-2 {{ $item['active'] ? 'bg-white/10 border-[#C8102E] text-white font-semibold' : 'border-transparent text-blue-100/70 font-medium hover:text-white hover:bg-white/5' }}">
                    <i class="fas {{ $item['icon'] }} w-4 text-center text-xs"></i> {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </div>
</nav>

<!-- Bawah: profil & keluar -->
<div class="px-4 py-6 border-t border-white/10">
    <div class="space-y-1">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="side-link w-full flex items-center gap-3 px-3 py-2.5 rounded-md text-sm border-l-2 border-transparent text-rose-200 font-medium hover:text-white hover:bg-[#C8102E]/30 hover:border-[#C8102E]">
                <i class="fas fa-sign-out-alt w-4 text-center text-xs"></i> Keluar
            </button>
        </form>
    </div>
</div>
