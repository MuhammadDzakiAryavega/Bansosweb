{{-- Formulir berita. Dipakai bersama oleh halaman tambah & ubah.
     Variabel: $berita (null saat tambah), $aksi, $metode, $labelTombol, daftar pilihan. --}}

@php
    $nilai = fn ($kolom, $bawaan = null) => old($kolom, $berita->{$kolom} ?? $bawaan);
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

<form method="POST" action="{{ $aksi }}" enctype="multipart/form-data">
    @csrf
    @if ($metode !== 'POST')
        @method($metode)
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

        <!-- ================= ISI UTAMA ================= -->
        <div class="lg:col-span-8">
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Isi Berita</h2>
                </div>

                <div class="px-6 py-6 lg:px-7 space-y-6">
                    <div>
                        <label for="judul_berita" class="block text-sm font-semibold text-slate-700 mb-2">
                            Judul Berita <span class="text-[#C8102E]">*</span>
                        </label>
                        <input type="text" id="judul_berita" name="judul_berita" value="{{ $nilai('judul_berita') }}"
                               maxlength="150" placeholder="Contoh: Penyaluran Bantuan PKH Tahap III Tahun 2026"
                               class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('judul_berita') border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E] @else border-slate-300 focus:border-[#14346B] focus:ring-[#14346B] @enderror">
                        @error('judul_berita')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="isi_artikel" class="block text-sm font-semibold text-slate-700 mb-2">
                            Isi Artikel <span class="text-[#C8102E]">*</span>
                        </label>
                        <textarea id="isi_artikel" name="isi_artikel" rows="16"
                                  placeholder="Tulis isi berita di sini. Pisahkan antar paragraf dengan baris kosong."
                                  class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 leading-relaxed placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('isi_artikel') border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E] @else border-slate-300 focus:border-[#14346B] focus:ring-[#14346B] @enderror">{{ $nilai('isi_artikel') }}</textarea>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Teks ditampilkan apa adanya di portal pengguna, termasuk pemisahan barisnya.
                        </p>
                        @error('isi_artikel')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PENGATURAN ================= -->
        <aside class="lg:col-span-4 space-y-6">

            <!-- Publikasi -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="bg-[#14346B] px-5 py-4">
                    <h2 class="text-white font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-sliders text-xs opacity-80"></i> Pengaturan Publikasi
                    </h2>
                    <p class="text-blue-100/80 text-xs mt-1">Kategori, penulis, dan status terbit</p>
                </div>

                <div class="px-5 py-5 space-y-5">
                    <div>
                        <label for="kategori" class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                        <select id="kategori" name="kategori"
                                class="w-full px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                            @foreach ($kategoriList as $kategori)
                                <option value="{{ $kategori }}" @selected($nilai('kategori') === $kategori)>{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="penulis" class="block text-sm font-semibold text-slate-700 mb-2">Penulis</label>
                        <select id="penulis" name="penulis"
                                class="w-full px-4 py-3 rounded-md border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#14346B] focus:ring-1 focus:ring-[#14346B] transition">
                            @foreach ($penulisList as $penulis)
                                <option value="{{ $penulis }}" @selected($nilai('penulis') === $penulis)>{{ $penulis }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <span class="block text-sm font-semibold text-slate-700 mb-2">Status</span>
                        <div class="space-y-2">
                            @php $statusTerpilih = $nilai('status_berita', 'Draft'); @endphp
                            @foreach ($statusList as $status)
                                <label class="flex items-start gap-3 px-4 py-3 rounded-md border cursor-pointer transition-colors {{ $statusTerpilih === $status ? 'border-[#14346B] bg-[#14346B]/5' : 'border-slate-300 hover:border-slate-400' }}">
                                    <input type="radio" name="status_berita" value="{{ $status }}"
                                           @checked($statusTerpilih === $status)
                                           class="mt-0.5 accent-[#14346B]">
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-800">
                                            {{ $status === 'Published' ? 'Published (Terbit)' : 'Draft (Draf)' }}
                                        </span>
                                        <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed">
                                            {{ $status === 'Published'
                                                ? 'Langsung tampil di halaman Berita portal pengguna.'
                                                : 'Disimpan saja, belum terlihat oleh masyarakat.' }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gambar sampul -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden"
                 x-data="{ pratinjau: '{{ $berita->gambar_cover ?? '' }}' }">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Gambar Sampul</h2>
                </div>

                <div class="px-5 py-5">
                    <div class="border border-slate-200 rounded-md overflow-hidden bg-slate-50 aspect-[16/9] flex items-center justify-center">
                        <img :src="pratinjau" x-show="pratinjau" x-cloak alt="Pratinjau sampul"
                             class="w-full h-full object-cover">
                        <span x-show="!pratinjau" class="text-slate-300 text-2xl">
                            <i class="fas fa-image"></i>
                        </span>
                    </div>

                    <label for="gambar_cover" class="block text-sm font-semibold text-slate-700 mt-4 mb-2">
                        {{ isset($berita) && $berita->gambar_cover ? 'Ganti Gambar' : 'Unggah Gambar' }}
                    </label>
                    <input type="file" id="gambar_cover" name="gambar_cover" accept="image/*"
                           @change="pratinjau = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : pratinjau"
                           class="w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#14346B] file:text-white hover:file:bg-[#0E2650] file:cursor-pointer border border-slate-300 rounded-md p-2">
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB. Rasio yang disarankan 16:9.
                        @if (isset($berita) && $berita->gambar_cover)
                            Kosongkan bila tidak ingin mengganti gambar.
                        @endif
                    </p>
                    @error('gambar_cover')
                        <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Aksi -->
            <div class="border border-slate-200 rounded-lg bg-white px-5 py-5 space-y-3">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-[#14346B] text-white py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                    <i class="fas fa-floppy-disk text-xs"></i> {{ $labelTombol }}
                </button>
                <a href="{{ route('admin.berita.index') }}"
                   class="w-full inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                    Batal
                </a>
            </div>
        </aside>
    </div>
</form>
