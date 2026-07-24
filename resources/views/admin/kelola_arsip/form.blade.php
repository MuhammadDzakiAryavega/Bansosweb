{{-- Formulir arsip. Dipakai bersama oleh halaman tambah & ubah.
     Variabel: $arsip (null saat tambah), $aksi, $metode, $labelTombol,
     $klasifikasiList, $statusList, $nomorUsulan (hanya saat tambah). --}}

@php
    $nilai = fn ($kolom, $bawaan = null) => old($kolom, $arsip->{$kolom} ?? $bawaan);

    $tglDokumen = old('tgl_dokumen', isset($arsip) ? $arsip->tgl_dokumen->format('Y-m-d') : '');
    $klasifikasiNilai = $nilai('klasifikasi', '');
    $statusTerpilih = $nilai('status_publikasi', 'Draft');

    $inputOk  = 'border-slate-300 focus:border-[#14346B] focus:ring-[#14346B]';
    $inputErr = 'border-rose-300 focus:border-[#C8102E] focus:ring-[#C8102E]';
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

        <!-- ================= IDENTITAS DOKUMEN ================= -->
        <div class="lg:col-span-8 space-y-6">
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Identitas Dokumen</h2>
                </div>

                <div class="px-6 py-6 lg:px-7 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nomor_arsip" class="block text-sm font-semibold text-slate-700 mb-2">
                                Nomor Arsip <span class="text-[#C8102E]">*</span>
                            </label>
                            <input type="text" id="nomor_arsip" name="nomor_arsip"
                                   value="{{ $nilai('nomor_arsip', $nomorUsulan ?? '') }}"
                                   maxlength="60" placeholder="ARS/001/X/2026"
                                   class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 tabular-nums tracking-wide placeholder:text-slate-400 placeholder:tracking-normal focus:outline-none focus:ring-1 transition @error('nomor_arsip') {{ $inputErr }} @else {{ $inputOk }} @enderror">
                            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                Pola: <span class="font-semibold text-slate-600">ARS / urutan / bulan romawi / tahun</span>.
                                @unless (isset($arsip))
                                    Nomor di atas adalah usulan otomatis, boleh diubah.
                                @endunless
                            </p>
                            @error('nomor_arsip')
                                <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tgl_dokumen" class="block text-sm font-semibold text-slate-700 mb-2">
                                Tanggal Dokumen <span class="text-[#C8102E]">*</span>
                            </label>
                            <input type="date" id="tgl_dokumen" name="tgl_dokumen" value="{{ $tglDokumen }}"
                                   class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 focus:outline-none focus:ring-1 transition @error('tgl_dokumen') {{ $inputErr }} @else {{ $inputOk }} @enderror">
                            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                Tanggal, bulan, dan tahun dokumen diterbitkan atau diterima.
                            </p>
                            @error('tgl_dokumen')
                                <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="judul_arsip" class="block text-sm font-semibold text-slate-700 mb-2">
                            Judul Arsip <span class="text-[#C8102E]">*</span>
                        </label>
                        <input type="text" id="judul_arsip" name="judul_arsip" value="{{ $nilai('judul_arsip') }}"
                               maxlength="255" placeholder="Contoh: Surat Undangan Rapat Koordinasi Penyaluran PKH Tahap III"
                               class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('judul_arsip') {{ $inputErr }} @else {{ $inputOk }} @enderror">
                        @error('judul_arsip')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deskripsi_tambahan" class="block text-sm font-semibold text-slate-700 mb-2">
                            Deskripsi Tambahan
                        </label>
                        <textarea id="deskripsi_tambahan" name="deskripsi_tambahan" rows="8" maxlength="2000"
                                  placeholder="Keterangan ringkas: asal/tujuan surat, perihal, tindak lanjut, atau letak berkas fisik."
                                  class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 leading-relaxed placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('deskripsi_tambahan') {{ $inputErr }} @else {{ $inputOk }} @enderror">{{ $nilai('deskripsi_tambahan') }}</textarea>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Bersifat opsional, maksimal 2000 karakter.
                        </p>
                        @error('deskripsi_tambahan')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ================= LAMPIRAN ================= -->
            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden"
                 x-data="{ berkasBaru: '', hapus: {{ old('hapus_lampiran') ? 'true' : 'false' }} }">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-3">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Lampiran Dokumen</h2>
                    <span class="text-[11px] font-semibold px-3 py-1 rounded-md border {{ isset($arsip) && $arsip->adaLampiran() ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                        {{ isset($arsip) && $arsip->adaLampiran() ? 'Ada berkas' : 'Belum ada berkas' }}
                    </span>
                </div>

                <div class="px-6 py-6 lg:px-7">

                    @if (isset($arsip) && $arsip->adaLampiran())
                        <div class="flex flex-wrap items-center gap-4 border border-slate-200 rounded-md bg-slate-50 px-5 py-4 mb-5"
                             :class="hapus && 'opacity-50'">
                            <span class="w-10 h-10 rounded-md bg-white border border-slate-200 text-[#14346B] flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $arsip->ikonLampiran() }}"></i>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-slate-800 truncate">{{ $arsip->lampiran_nama }}</span>
                                <span class="block text-xs text-slate-500 mt-0.5">{{ $arsip->ukuranLampiran() }}</span>
                            </span>
                            <a href="{{ route('admin.arsip.unduh', $arsip->id_arsip) }}"
                               class="inline-flex items-center gap-2 border border-slate-300 text-slate-700 px-4 py-2.5 rounded-md text-xs font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                                <i class="fas fa-download text-[10px]"></i> Unduh
                            </a>
                        </div>

                        <label class="flex items-start gap-3 px-4 py-3 rounded-md border cursor-pointer transition-colors mb-5"
                               :class="hapus ? 'border-[#C8102E] bg-[#C8102E]/5' : 'border-slate-300 hover:border-slate-400'">
                            <input type="checkbox" name="hapus_lampiran" value="1" x-model="hapus"
                                   class="mt-0.5 accent-[#C8102E]">
                            <span>
                                <span class="block text-sm font-semibold text-slate-800">Hapus lampiran ini</span>
                                <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed">
                                    Berkas dihapus permanen saat perubahan disimpan.
                                </span>
                            </span>
                        </label>
                    @endif

                    <label for="lampiran" class="block text-sm font-semibold text-slate-700 mb-2">
                        {{ isset($arsip) && $arsip->adaLampiran() ? 'Ganti dengan Berkas Baru' : 'Unggah Berkas' }}
                    </label>
                    <input type="file" id="lampiran" name="lampiran"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                           @change="berkasBaru = $event.target.files[0] ? $event.target.files[0].name : ''"
                           class="w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#14346B] file:text-white hover:file:bg-[#0E2650] file:cursor-pointer border rounded-md p-2 @error('lampiran') border-rose-300 @else border-slate-300 @enderror">
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        Format PDF, DOC, DOCX, XLS, XLSX, JPG, atau PNG. Ukuran maksimal 5 MB.
                        Berkas tersimpan di penyimpanan internal dan hanya bisa diunduh oleh administrator.
                    </p>
                    <p x-show="berkasBaru" x-cloak class="mt-2 text-xs text-[#14346B] font-semibold">
                        <i class="fas fa-paperclip mr-1"></i> <span x-text="berkasBaru"></span>
                    </p>
                    @error('lampiran')
                        <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- ================= KLASIFIKASI & PUBLIKASI ================= -->
        <aside class="lg:col-span-4 space-y-6">

            <div class="border border-slate-200 rounded-lg bg-white overflow-hidden"
                 x-data="{ klasifikasi: @js($klasifikasiNilai) }">
                <div class="bg-[#14346B] px-5 py-4">
                    <h2 class="text-white font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-sliders text-xs opacity-80"></i> Klasifikasi &amp; Publikasi
                    </h2>
                    <p class="text-blue-100/80 text-xs mt-1">Kategori dokumen dan status pencatatan</p>
                </div>

                <div class="px-5 py-5 space-y-5">
                    <div>
                        <label for="klasifikasi" class="block text-sm font-semibold text-slate-700 mb-2">
                            Klasifikasi / Kategori <span class="text-[#C8102E]">*</span>
                        </label>
                        <select id="klasifikasi" name="klasifikasi" x-model="klasifikasi"
                                class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 focus:outline-none focus:ring-1 transition @error('klasifikasi') {{ $inputErr }} @else {{ $inputOk }} @enderror">
                            <option value="">-- Pilih klasifikasi --</option>
                            @foreach ($klasifikasiList as $klasifikasi)
                                <option value="{{ $klasifikasi }}" @selected($klasifikasiNilai === $klasifikasi)>{{ $klasifikasi }}</option>
                            @endforeach
                            <option value="__baru__" @selected($klasifikasiNilai === '__baru__')>Lainnya (buat klasifikasi baru)</option>
                        </select>
                        @error('klasifikasi')
                            <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                        @enderror

                        <div x-show="klasifikasi === '__baru__'" x-cloak class="mt-3">
                            <label for="klasifikasi_baru" class="block text-sm font-semibold text-slate-700 mb-2">
                                Nama Klasifikasi Baru <span class="text-[#C8102E]">*</span>
                            </label>
                            <input type="text" id="klasifikasi_baru" name="klasifikasi_baru"
                                   value="{{ old('klasifikasi_baru') }}" maxlength="60"
                                   placeholder="Contoh: Nota Dinas"
                                   class="w-full px-4 py-3 rounded-md border text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 transition @error('klasifikasi_baru') {{ $inputErr }} @else {{ $inputOk }} @enderror">
                            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                Klasifikasi baru otomatis ikut tersedia pada arsip berikutnya.
                            </p>
                            @error('klasifikasi_baru')
                                <p class="mt-2 text-xs text-[#C8102E]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <span class="block text-sm font-semibold text-slate-700 mb-2">Status Publikasi</span>
                        <div class="space-y-2">
                            @foreach ($statusList as $status)
                                <label class="flex items-start gap-3 px-4 py-3 rounded-md border cursor-pointer transition-colors {{ $statusTerpilih === $status ? 'border-[#14346B] bg-[#14346B]/5' : 'border-slate-300 hover:border-slate-400' }}">
                                    <input type="radio" name="status_publikasi" value="{{ $status }}"
                                           @checked($statusTerpilih === $status)
                                           class="mt-0.5 accent-[#14346B]">
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-800">
                                            {{ $status === 'Published' ? 'Published (Dipublikasikan)' : 'Draft (Draf)' }}
                                        </span>
                                        <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed">
                                            {{ $status === 'Published'
                                                ? 'Arsip dinyatakan final dan resmi tercatat.'
                                                : 'Masih dalam pengerjaan atau menunggu berkas lengkap.' }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs text-slate-500 leading-relaxed">
                            <i class="fas fa-lock mr-1 opacity-70"></i>
                            Arsip tidak ditampilkan di portal pengguna. Status ini hanya penanda internal admin.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Aksi -->
            <div class="border border-slate-200 rounded-lg bg-white px-5 py-5 space-y-3">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-[#14346B] text-white py-3 rounded-md text-sm font-semibold hover:bg-[#0E2650] transition-colors">
                    <i class="fas fa-floppy-disk text-xs"></i> {{ $labelTombol }}
                </button>
                <a href="{{ route('admin.arsip.index') }}"
                   class="w-full inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 py-3 rounded-md text-sm font-semibold hover:border-[#14346B] hover:text-[#14346B] transition-colors">
                    Batal
                </a>
            </div>
        </aside>
    </div>
</form>
