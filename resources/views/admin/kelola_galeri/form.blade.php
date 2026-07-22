{{-- Formulir galeri kegiatan. Dipakai bersama oleh halaman tambah & ubah.
     Variabel: $galeri (null saat tambah), $aksi, $metode, $labelTombol. --}}

@php
    $nilai = fn ($kolom, $bawaan = null) => old($kolom, $galeri->{$kolom} ?? $bawaan);
    $fotoTersimpan = $galeri?->fotos ?? collect();
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
        <div class="lg:col-span-8 space-y-6">

            <!-- Informasi kegiatan -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Informasi Kegiatan</h2>
                </div>

                <div class="px-6 py-6 lg:px-7 space-y-6">
                    <div>
                        <label for="judul_kegiatan" class="block text-sm font-semibold text-slate-700 mb-2">
                            Judul Kegiatan <span class="text-[#C8102E]">*</span>
                        </label>
                        <input type="text" id="judul_kegiatan" name="judul_kegiatan" value="{{ $nilai('judul_kegiatan') }}"
                               maxlength="255" placeholder="Contoh: Penyaluran Bantuan PKH Tahap III Kecamatan Teramang Jaya"
                               class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('judul_kegiatan') border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E] @else border-slate-300 focus:border-[#14346B] focus:ring-[#14346B] @enderror">
                        @error('judul_kegiatan')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tgl_pelaksanaan" class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Pelaksanaan <span class="text-[#C8102E]">*</span>
                        </label>
                        <input type="date" id="tgl_pelaksanaan" name="tgl_pelaksanaan"
                               value="{{ old('tgl_pelaksanaan', $galeri?->tgl_pelaksanaan?->format('Y-m-d')) }}"
                               class="w-full md:max-w-xs px-4 py-3 rounded-md border text-sm text-slate-800 focus:outline-none focus:ring-1 transition @error('tgl_pelaksanaan') border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E] @else border-slate-300 focus:border-[#14346B] focus:ring-[#14346B] @enderror">
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Tanggal kegiatan berlangsung. Dipakai untuk mengurutkan galeri di portal pengguna.
                        </p>
                        @error('tgl_pelaksanaan')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ isi: @js((string) $nilai('deskripsi_singkat', '')) }">
                        <label for="deskripsi_singkat" class="block text-sm font-semibold text-slate-700 mb-2">
                            Deskripsi Singkat <span class="text-[#C8102E]">*</span>
                        </label>
                        <textarea id="deskripsi_singkat" name="deskripsi_singkat" rows="6" maxlength="1000" x-model="isi"
                                  placeholder="Jelaskan secara ringkas kegiatan yang didokumentasikan: tempat, peserta, dan tujuan kegiatan."
                                  class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 leading-relaxed placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('deskripsi_singkat') border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E] @else border-slate-300 focus:border-[#14346B] focus:ring-[#14346B] @enderror">{{ $nilai('deskripsi_singkat') }}</textarea>
                        <div class="mt-2 flex items-start justify-between gap-4">
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Teks ditampilkan apa adanya di portal pengguna, termasuk pemisahan barisnya.
                            </p>
                            <span class="text-xs text-slate-400 tabular-nums flex-shrink-0"
                                  x-text="isi.length + ' / 1000'">0 / 1000</span>
                        </div>
                        @error('deskripsi_singkat')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Media & dokumentasi -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden"
                 x-data="{
                     hapus: [],
                     baru: [],
                     pilihBerkas(event) {
                         this.baru = Array.from(event.target.files).map(function (berkas) {
                             return { nama: berkas.name, url: URL.createObjectURL(berkas) };
                         });
                     }
                 }">
                <div class="bg-[#14346B] px-5 py-4">
                    <h2 class="text-white font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-photo-film text-xs opacity-80"></i> Media &amp; Dokumentasi
                    </h2>
                    <p class="text-blue-100/80 text-xs mt-1">Foto kegiatan yang ditampilkan pada galeri portal pengguna</p>
                </div>

                <div class="px-6 py-6 lg:px-7 space-y-6">

                    @if ($fotoTersimpan->isNotEmpty())
                        <div>
                            <div class="flex items-center justify-between gap-4 mb-3">
                                <span class="block text-sm font-semibold text-slate-700">
                                    Dokumentasi Tersimpan ({{ $fotoTersimpan->count() }})
                                </span>
                                <span class="text-xs text-slate-500" x-show="hapus.length > 0" x-cloak>
                                    <span x-text="hapus.length"></span> foto akan dihapus saat disimpan
                                </span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach ($fotoTersimpan as $foto)
                                    <label class="group relative block rounded-md overflow-hidden border cursor-pointer transition-colors"
                                           :class="hapus.includes({{ $foto->id_foto }}) ? 'border-[#C8102E]' : 'border-slate-200 hover:border-[#14346B]'">
                                        <input type="checkbox" name="hapus_foto[]" value="{{ $foto->id_foto }}"
                                               x-model.number="hapus" class="sr-only">
                                        <img src="{{ $foto->path }}" alt="Dokumentasi kegiatan"
                                             class="w-full aspect-[4/3] object-cover transition-opacity"
                                             :class="hapus.includes({{ $foto->id_foto }}) ? 'opacity-30' : ''">
                                        <span class="absolute top-1.5 right-1.5 w-7 h-7 rounded-md flex items-center justify-center text-[11px] transition-colors"
                                              :class="hapus.includes({{ $foto->id_foto }}) ? 'bg-[#C8102E] text-white' : 'bg-white/90 text-slate-600 border border-slate-200'">
                                            <i class="fas" :class="hapus.includes({{ $foto->id_foto }}) ? 'fa-rotate-left' : 'fa-trash'"></i>
                                        </span>
                                        <span class="absolute inset-x-0 bottom-0 bg-[#C8102E] text-white text-[10px] font-semibold text-center py-1"
                                              x-show="hapus.includes({{ $foto->id_foto }})" x-cloak>
                                            Ditandai untuk dihapus
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-3 text-xs text-slate-500 leading-relaxed">
                                Klik foto untuk menandainya agar dihapus. Penghapusan baru berlaku setelah formulir disimpan.
                            </p>
                        </div>
                    @endif

                    <div>
                        <label for="foto" class="block text-sm font-semibold text-slate-700 mb-2">
                            {{ $fotoTersimpan->isNotEmpty() ? 'Tambah Foto Dokumentasi' : 'Unggah Dokumentasi Foto' }}
                        </label>
                        <input type="file" id="foto" name="foto[]" accept="image/*" multiple
                               @change="pilihBerkas($event)"
                               class="w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#14346B] file:text-white hover:file:bg-[#0E2650] file:cursor-pointer border border-slate-300 rounded-md p-2">
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB per foto, maksimal 20 foto sekali unggah.
                            Bisa memilih beberapa berkas sekaligus dengan menahan tombol Ctrl.
                        </p>
                        @error('foto')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                        @foreach ($errors->get('foto.*') as $pesanFoto)
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $pesanFoto[0] }}</p>
                        @endforeach

                        <!-- Pratinjau berkas yang baru dipilih -->
                        <div x-show="baru.length > 0" x-cloak class="mt-4">
                            <span class="block text-sm font-semibold text-slate-700 mb-3">
                                Pratinjau Unggahan (<span x-text="baru.length"></span>)
                            </span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                <template x-for="(berkas, i) in baru" :key="i">
                                    <div class="rounded-md overflow-hidden border border-slate-200">
                                        <img :src="berkas.url" :alt="berkas.nama" class="w-full aspect-[4/3] object-cover">
                                        <p class="px-2 py-1.5 text-[10px] text-slate-500 truncate bg-slate-50" x-text="berkas.nama"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= PANEL SAMPING ================= -->
        <aside class="lg:col-span-4 space-y-6">

            <!-- Panduan singkat -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Panduan Pengisian</h2>
                </div>
                <div class="px-5 py-5">
                    <ul class="space-y-3.5 text-xs text-slate-600 leading-relaxed">
                        <li class="flex gap-3">
                            <i class="fas fa-circle-check text-[#14346B] mt-0.5 w-3.5 text-center flex-shrink-0"></i>
                            <span>Gunakan judul kegiatan yang jelas, misalnya menyertakan tahap dan lokasi penyaluran.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-circle-check text-[#14346B] mt-0.5 w-3.5 text-center flex-shrink-0"></i>
                            <span>Foto pertama dipakai sebagai sampul kartu galeri di portal pengguna.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-circle-check text-[#14346B] mt-0.5 w-3.5 text-center flex-shrink-0"></i>
                            <span>Pastikan dokumentasi tidak menampilkan data pribadi penerima seperti NIK atau nomor rekening.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-circle-check text-[#14346B] mt-0.5 w-3.5 text-center flex-shrink-0"></i>
                            <span>Galeri langsung tampil di portal pengguna setelah disimpan.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Aksi -->
            <div class="border border-slate-200 rounded-lg bg-white px-5 py-5 space-y-3">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-[#14346B] text-white py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                    <i class="fas fa-floppy-disk text-xs"></i> {{ $labelTombol }}
                </button>
                <a href="{{ route('admin.galeri.index') }}"
                   class="w-full inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                    Batal
                </a>
            </div>
        </aside>
    </div>
</form>
