# ARAHAN PENULISAN BAB III — ANALISIS DAN PERANCANGAN

> **Cara pakai:** buka Claude di web, unggah/lampirkan file `LAPORAN_TUGAS_AKHIR.docx` (berisi BAB I & BAB II yang sudah jadi) dan `Panduan-TA-D3-Jurusan-TI.pdf`, lalu tempelkan **seluruh isi file ini** sebagai prompt. File ini sudah memuat seluruh fakta sistem, jadi Claude tidak perlu menebak apa pun.

---

## 1. PERAN DAN TUGAS

Bertindaklah sebagai pendamping penulisan Tugas Akhir D3 Manajemen Informatika. Tugasmu: **menulis BAB III ANALISIS DAN PERANCANGAN secara lengkap dan siap tempel ke Microsoft Word**, mengikuti kerangka panduan jurusan (Bagian 4), memakai fakta sistem pada Bagian 5 sebagai satu-satunya sumber kebenaran, dan mengikuti instruksi isi per sub-bab pada Bagian 6.

Keluaran ditulis dalam **bahasa Indonesia baku, kalimat pasif, gaya laporan akademik**, konsisten dengan gaya BAB I dan BAB II yang sudah ada.

---

## 2. IDENTITAS DAN KONTEKS TUGAS AKHIR

| Item | Isi |
|---|---|
| Judul | Penerapan Metode SAW pada Sistem Pendukung Keputusan Penerima PKH Berbasis Laravel (Studi Kasus: Kecamatan Teramang Jaya) |
| Penulis | Muhammad Dzaki Aryavega (2301093018) |
| Program Studi | D3 Manajemen Informatika, Jurusan Teknologi Informasi, Politeknik Negeri Padang |
| Tahun | 2026 |
| Objek penelitian | Dinas Sosial Kabupaten Mukomuko, wilayah Kecamatan Teramang Jaya (13 desa) |
| Metode pengambilan keputusan | *Simple Additive Weighting* (SAW) |
| Metode pengembangan | *Waterfall* (sesuai BAB I sub-bab 1.6) |
| Pengujian | *Black box testing* (dibahas di BAB IV, **bukan** di BAB III) |

Rumusan masalah dan tujuan pada BAB I (ringkas): membangun SPK penerima PKH berbasis web dengan Laravel, menerapkan metode SAW untuk perhitungan dan perankingan, serta menguji fungsionalitas dengan *black box testing*. BAB III harus terbaca sebagai jawaban perancangan atas rumusan masalah nomor 1 dan 2.

---

## 3. ATURAN PENULISAN WAJIB (dari Panduan TA D3 TI PNP)

1. Bahasa Indonesia baku, EYD; **dilarang memakai kata ganti orang** (tidak boleh "saya", "kita", "kami", "penulis melakukan…") — gunakan kalimat pasif: "dirancang", "dilakukan", "digunakan", "diperoleh".
2. Istilah asing yang belum ada padanannya ditulis *miring* (italic), contoh: *use case*, *benefit*, *cost*, *framework*, *black box*.
3. Sitasi memakai gaya **IEEE** dengan nomor dalam kurung siku, contoh `[5]`. **Dilarang membuat referensi baru.** Bila perlu mengutip, gunakan hanya nomor yang sudah ada di daftar pustaka:
   - `[3]` Letelay — SAW untuk kelayakan penerima PKH
   - `[5]` An'syah & Widyasari — implementasi SAW bantuan sosial
   - `[6]` Awaluddin dkk. — Laravel/MVC
   - `[11]` Hartono — flowchart
   - `[12]` Hendini — UML
   - `[13]` Afiifah dkk. — ERD
   - `[14]` Mesterjon & Siska — PHP/MySQL
   BAB III adalah bab perancangan, jadi sitasi seperlunya saja (boleh sangat sedikit).
4. Penomoran sub-bab desimal tanpa titik akhir: `3.1`, `3.1.1`, `3.2`, dst.
5. Penomoran tabel dan gambar mengikuti gaya yang **sudah dipakai pada BAB II dokumen ini**: `Tabel 3. 1`, `Tabel 3. 2`, `Gambar 3. 1`, `Gambar 3. 2`, dan seterusnya (nomor bab, spasi, nomor urut). Judul tabel diletakkan **di atas** tabel, judul gambar **di bawah** gambar.
6. Setiap tabel dan gambar **wajib dirujuk di dalam kalimat** sebelum ditampilkan, contoh: "Kriteria beserta bobotnya dapat dilihat pada Tabel 3. 2."
7. Setiap sub-bab minimal berisi paragraf pengantar; dilarang langsung menampilkan tabel/gambar tanpa penjelasan.
8. Font, margin, dan spasi diatur di Word (Times New Roman 12, spasi 1,5) — cukup pastikan struktur teksnya rapi dan mudah ditempel.

---

## 4. KERANGKA BAB III YANG WAJIB DIIKUTI

Kerangka berikut diambil persis dari Panduan TA D3 Manajemen Informatika (BAB V Template Laporan TA) dan **tidak boleh diubah urutan maupun penomorannya**:

```
BAB III ANALISIS DAN PERANCANGAN
3.1   Analisis
      3.1.1  Analisis Sistem yang Sedang Berjalan
      3.1.2  Analisis Sistem yang Diajukan
      3.1.3  Analisis Penerapan Metode SAW        (tambahan, lihat catatan)
3.2   Rancangan Struktur Sistem yang Akan Dibangun
3.3   Rancangan Sistem
      3.3.1  Rancangan Arsitektur
      3.3.2  Use Case Diagram
      3.3.3  Class Diagram
      3.3.4  Sequence Diagram
      3.3.5  Activity Diagram
3.4   Entity Relationship Diagram (ERD)
3.5   Perancangan Basis Data
3.6   Perancangan Antarmuka (Interface)
```

**Catatan sub-bab 3.1.3:** panduan tidak mencantumkannya secara eksplisit, namun perhitungan SAW adalah inti penelitian dan paling tepat diletakkan di dalam bagian analisis. Tulislah sub-bab ini, dan di akhir keluaran berikan satu baris catatan kecil untuk penulis: *"3.1.3 merupakan tambahan di luar kerangka baku panduan — mohon dikonfirmasikan kepada dosen pembimbing."*

---

## 5. FAKTA SISTEM (SUMBER KEBENARAN — DILARANG MENGARANG)

Seluruh data di bawah ini diambil langsung dari kode sistem yang sudah dibangun. **Dilarang menambah fitur, tabel, kolom, kriteria, atau aktor yang tidak tercantum di sini.**

### 5.1 Teknologi yang digunakan

- Bahasa: PHP 8.2+ (dikembangkan pada PHP 8.4)
- *Framework*: Laravel 12 dengan pola arsitektur MVC
- Basis data: MySQL
- Tampilan: Blade *template engine*, Tailwind CSS, Alpine.js, ikon Font Awesome
- Penyimpanan berkas: *disk* lokal privat (`storage/app/private`) untuk foto pendaftaran dan lampiran arsip
- Autentikasi: bawaan Laravel (sesi), ditambah *middleware* `EnsureUserIsAdmin` dengan alias `admin` untuk membatasi area administrator

### 5.2 Aktor dan hak akses

| Aktor | Keterangan | Hak akses |
|---|---|---|
| Pengunjung (*guest*) | Belum memiliki akun / belum masuk | Melihat beranda, halaman tentang, berita, galeri; mendaftar akun; masuk |
| Masyarakat (`role = user`) | Warga Kecamatan Teramang Jaya | Seluruh hak pengunjung + mengelola profil, mengubah kata sandi, mengajukan pendaftaran PKH, mengirim pengaduan, melihat riwayat pengaduan, keluar |
| Admin / Petugas (`role = admin`) | Petugas Dinas Sosial | Mengelola berita, galeri, arsip, pengguna, pengaduan, serta seluruh modul Kelola PKH (kriteria & sub-kriteria, pendaftaran masuk, penilaian calon, hasil akhir) |

### 5.3 Alur sistem yang sedang berjalan (kondisi awal, hasil wawancara & observasi)

1. Warga menyampaikan usulan/berkas kepada petugas atau pendamping PKH di desa.
2. Petugas mencatat data calon penerima dan merekapitulasinya ke dalam berkas *spreadsheet*.
3. Petugas mencocokkan kondisi tiap calon dengan kriteria kelayakan satu per satu secara manual.
4. Penilaian kelayakan bersifat kualitatif sehingga rawan subjektivitas dan *human error*.
5. Hasil rekapitulasi diserahkan kepada pihak berwenang untuk ditetapkan.

Kelemahan: waktu proses lama saat data bertambah, tidak ada perhitungan terbobot yang baku, tidak ada urutan prioritas (perankingan) yang terukur, berkas bukti tersebar dan sulit ditelusuri, serta hasil sulit dipertanggungjawabkan secara objektif.

### 5.4 Alur sistem yang diajukan

1. **Pendaftaran (warga, wajib masuk akun).** Warga mengisi formulir pendaftaran PKH: data diri (nama, NIK 16 digit, desa, alamat, nomor telepon) dan data kondisi ekonomi mandiri (*self-report*) berupa penghasilan, jumlah tanggungan, kondisi rumah, status pekerjaan, dan kepemilikan aset. Warga wajib mengunggah **5 foto**: tampak depan rumah, tampak belakang rumah, ruang tamu, WC/kamar mandi, dan foto diri sambil memegang KTP. Format JPG/PNG, maksimal 5 MB per berkas, disimpan pada *disk* privat. Sistem menolak pengajuan ganda apabila warga masih memiliki pengajuan berstatus `Baru` atau `Diverifikasi`.
2. **Pendaftaran Masuk (admin).** Admin meninjau daftar pengajuan (dapat disaring menurut status, desa, dan kata kunci nama/NIK), membuka detail pengajuan beserta kelima foto, lalu **memverifikasi** atau **menolak** disertai catatan. Pengajuan yang diverifikasi otomatis menjadikan warga tersebut sebagai **alternatif** (calon penerima), dan nilai desanya disalin dari data pendaftaran.
3. **Penilaian Calon (admin).** Untuk setiap alternatif, admin memilih satu **sub-kriteria** pada masing-masing dari 5 kriteria. Data *self-report* dan foto dari pendaftaran ditampilkan sebagai acuan agar penilaian tetap berbasis bukti. Alternatif juga dapat ditambahkan manual dari daftar warga terdaftar yang belum menjadi calon.
4. **Hasil Akhir (admin).** Sistem membentuk matriks keputusan dari nilai *crisp* sub-kriteria, melakukan normalisasi SAW, menghitung nilai preferensi, dan menampilkan perankingan dari nilai tertinggi. Alternatif yang penilaiannya belum lengkap dipisahkan dan tidak ikut dihitung. Tersedia penyaring desa: bila satu desa dipilih, **normalisasi dihitung hanya di dalam desa tersebut** (nilai maksimum tiap kolom diambil dari calon di desa itu saja).
5. Hasil perankingan bersifat **rekomendasi**; keputusan akhir tetap berada pada pihak berwenang.

### 5.5 Kriteria dan bobot

Kriteria bersifat tetap sesuai ketetapan Dinas Sosial dan **disimpan sebagai konstanta di dalam kode** (bukan tabel basis data), yaitu pada konstanta `KRITERIA` di kelas `App\Http\Controllers\Admin\PkhController`. Alasan perancangan: kriteria dan bobot tidak berubah-ubah sehingga cukup satu sumber acuan untuk menu, halaman kriteria, dan perhitungan; yang dapat dikelola admin adalah sub-kriteria (himpunan) di dalamnya.

| Kode | Kriteria | Bobot (W) | Jenis |
|---|---|---|---|
| C1 | Penghasilan | 0,30 | *Benefit* |
| C2 | Jumlah Tanggungan | 0,25 | *Benefit* |
| C3 | Kondisi Rumah | 0,20 | *Benefit* |
| C4 | Status Pekerjaan | 0,15 | *Benefit* |
| C5 | Kepemilikan Aset | 0,10 | *Benefit* |

Total bobot = 1,00. Seluruh kriteria berjenis *benefit* karena skala nilai *crisp* disusun terbalik: **semakin buruk kondisi ekonomi, semakin besar nilainya**, sehingga keluarga yang paling membutuhkan memperoleh nilai preferensi tertinggi. Poin ini **wajib dijelaskan** pada sub-bab 3.1.3 agar tidak dianggap keliru oleh penguji.

### 5.6 Sub-kriteria (himpunan) dan nilai *crisp* (skala 1–5)

| Kriteria | Sub-kriteria | Nilai |
|---|---|---|
| C1 Penghasilan | < Rp1.000.000 | 5 |
| | Rp1.000.000 – Rp2.000.000 | 4 |
| | Rp2.000.000 – Rp3.000.000 | 2 |
| | > Rp3.000.000 | 1 |
| C2 Jumlah Tanggungan | Lebih dari 4 orang | 5 |
| | 3 – 4 orang | 4 |
| | 1 – 2 orang | 2 |
| | Tidak ada tanggungan | 1 |
| C3 Kondisi Rumah | Tidak layak huni | 5 |
| | Kurang layak huni | 3 |
| | Layak huni | 1 |
| C4 Status Pekerjaan | Tidak bekerja | 5 |
| | Buruh / pekerja harian lepas | 4 |
| | Wiraswasta / usaha kecil | 2 |
| | Karyawan / pegawai tetap | 1 |
| C5 Kepemilikan Aset | Tidak memiliki aset | 5 |
| | Memiliki aset sederhana | 3 |
| | Memiliki aset bernilai | 1 |

Sub-kriteria dikelola admin (tambah, ubah, hapus) melalui halaman kriteria; nilai yang diizinkan 1–100 dengan validasi bilangan bulat.

### 5.7 Rumus SAW yang diterapkan sistem

Normalisasi:

- Kriteria *benefit*: `r_ij = x_ij / max(x_ij)`
- Kriteria *cost*: `r_ij = min(x_ij) / x_ij`

Nilai preferensi: `V_i = Σ (w_j × r_ij)`, untuk j = 1..5.

Catatan implementasi yang harus disebut: seluruh kriteria pada sistem ini *benefit*, sehingga yang dipakai adalah persamaan *benefit*; pembagian dijaga agar tidak terjadi pembagian dengan nol (bila nilai maksimum kolom 0, hasil normalisasi ditetapkan 0); hasil diurutkan menurun berdasarkan `V_i`.

### 5.8 Contoh perhitungan SAW (data uji terverifikasi — pakai angka ini apa adanya)

Matriks keputusan (X) dari 7 alternatif:

| Alternatif | Desa | C1 | C2 | C3 | C4 | C5 |
|---|---|---|---|---|---|---|
| A1 Sarni | Pasar Bantal | 5 | 5 | 5 | 5 | 5 |
| A2 Budi Hartono | Pasar Bantal | 2 | 2 | 3 | 2 | 3 |
| A3 Siti Aminah | Pasar Bantal | 4 | 4 | 5 | 4 | 3 |
| A4 Joko Susilo | Nenggalo | 5 | 4 | 3 | 4 | 5 |
| A5 Wati Lestari | Nenggalo | 2 | 2 | 1 | 1 | 1 |
| A6 Rahmat Hidayat | Nenggalo | 4 | 5 | 5 | 5 | 3 |
| A7 Yanti Marlina | Bandar Jaya | 4 | 4 | 3 | 4 | 3 |

Nilai maksimum tiap kolom: C1 = 5, C2 = 5, C3 = 5, C4 = 5, C5 = 5.

Matriks ternormalisasi (R) = setiap nilai dibagi 5:

| Alternatif | C1 | C2 | C3 | C4 | C5 |
|---|---|---|---|---|---|
| A1 | 1,00 | 1,00 | 1,00 | 1,00 | 1,00 |
| A2 | 0,40 | 0,40 | 0,60 | 0,40 | 0,60 |
| A3 | 0,80 | 0,80 | 1,00 | 0,80 | 0,60 |
| A4 | 1,00 | 0,80 | 0,60 | 0,80 | 1,00 |
| A5 | 0,40 | 0,40 | 0,20 | 0,20 | 0,20 |
| A6 | 0,80 | 1,00 | 1,00 | 1,00 | 0,60 |
| A7 | 0,80 | 0,80 | 0,60 | 0,80 | 0,60 |

Nilai preferensi dan perankingan (sudah diverifikasi, **jangan dihitung ulang dengan hasil berbeda**):

| Peringkat | Alternatif | V | Rincian |
|---|---|---|---|
| 1 | A1 Sarni | 1,0000 | (0,30×1,00)+(0,25×1,00)+(0,20×1,00)+(0,15×1,00)+(0,10×1,00) |
| 2 | A6 Rahmat Hidayat | 0,9000 | (0,30×0,80)+(0,25×1,00)+(0,20×1,00)+(0,15×1,00)+(0,10×0,60) |
| 3 | A4 Joko Susilo | 0,8400 | (0,30×1,00)+(0,25×0,80)+(0,20×0,60)+(0,15×0,80)+(0,10×1,00) |
| 4 | A3 Siti Aminah | 0,8200 | (0,30×0,80)+(0,25×0,80)+(0,20×1,00)+(0,15×0,80)+(0,10×0,60) |
| 5 | A7 Yanti Marlina | 0,7400 | (0,30×0,80)+(0,25×0,80)+(0,20×0,60)+(0,15×0,80)+(0,10×0,60) |
| 6 | A2 Budi Hartono | 0,4600 | (0,30×0,40)+(0,25×0,40)+(0,20×0,60)+(0,15×0,40)+(0,10×0,60) |
| 7 | A5 Wati Lestari | 0,3100 | (0,30×0,40)+(0,25×0,40)+(0,20×0,20)+(0,15×0,20)+(0,10×0,20) |

Tuliskan pula contoh perankingan **per desa** untuk Desa Pasar Bantal (A1, A2, A3): karena nilai maksimum tiap kolom di desa tersebut juga 5, hasilnya A1 = 1,0000; A3 = 0,8200; A2 = 0,4600. Jelaskan bahwa perbedaan hasil antara mode seluruh wilayah dan mode per desa muncul ketika nilai maksimum kolom di suatu desa berbeda dari nilai maksimum keseluruhan.

### 5.9 Daftar desa (kategori wilayah, 13 desa Kecamatan Teramang Jaya)

Pasar Bantal, Teramang Jaya, Nenggalo, Pondok Baru, Bunga Tanjung, Sido Makmur, Lubuk Selandak, Bandar Jaya, Mandi Angin Jaya, Nelan Indah, Pernyah, Batu Ejung, Brangan Mulya.

### 5.10 Struktur tabel basis data (nama tabel dan kolom sesuai migrasi Laravel)

**`users`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned | *Primary key*, *auto increment* |
| name | varchar(255) | Nama lengkap |
| nik | varchar(16) | *Unique*, boleh kosong |
| email | varchar(255) | *Unique* |
| role | varchar(255) | `user` / `admin`, bawaan `user` |
| email_verified_at | timestamp | Boleh kosong |
| password | varchar(255) | Tersimpan dalam bentuk *hash* |
| remember_token | varchar(100) | Token "ingat saya" |
| created_at, updated_at | timestamp | Waktu rekam |

**`pkh_pendaftaran`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned | *Primary key* |
| user_id | bigint unsigned | *Foreign key* → `users.id`, boleh kosong (*null on delete*) |
| nama | varchar(255) | Nama pengaju |
| nik | varchar(16) | NIK 16 digit |
| alamat | text | Alamat lengkap |
| desa | varchar(60) | Desa di Kecamatan Teramang Jaya |
| no_hp | varchar(20) | Boleh kosong |
| penghasilan | varchar(60) | Pilihan *self-report* |
| jumlah_tanggungan | smallint unsigned | Jumlah tanggungan |
| kondisi_rumah | varchar(60) | Pilihan *self-report* |
| status_pekerjaan | varchar(60) | Pilihan *self-report* |
| kepemilikan_aset | varchar(60) | Pilihan *self-report* |
| foto_depan | varchar(255) | Path foto tampak depan rumah |
| foto_belakang | varchar(255) | Path foto tampak belakang rumah |
| foto_ruang_tamu | varchar(255) | Path foto ruang tamu |
| foto_wc | varchar(255) | Path foto WC/kamar mandi |
| foto_ktp | varchar(255) | Path foto diri memegang KTP |
| status | enum | `Baru`, `Diverifikasi`, `Ditolak` (bawaan `Baru`), diindeks |
| catatan_admin | text | Catatan petugas saat menolak |
| created_at, updated_at | timestamp | Waktu rekam |

**`pkh_alternatif`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned | *Primary key* |
| user_id | bigint unsigned | *Foreign key* → `users.id`, **unique** (*cascade on delete*) |
| desa | varchar(60) | Disalin dari pendaftaran |
| created_at, updated_at | timestamp | Waktu rekam |

**`pkh_sub_kriteria`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned | *Primary key* |
| kriteria | varchar(40) | Slug kriteria: `penghasilan`, `jumlah-tanggungan`, `kondisi-rumah`, `status-pekerjaan`, `kepemilikan-aset` (diindeks) |
| nama | varchar(120) | Label himpunan, mis. "< Rp1.000.000" |
| nilai | tinyint unsigned | Nilai *crisp* |
| created_at, updated_at | timestamp | Waktu rekam |

**`pkh_penilaian`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned | *Primary key* |
| alternatif_id | bigint unsigned | *Foreign key* → `pkh_alternatif.id` (*cascade*) |
| kriteria | varchar(40) | Slug kriteria |
| sub_kriteria_id | bigint unsigned | *Foreign key* → `pkh_sub_kriteria.id` (*cascade*) |
| — | — | *Unique* gabungan (`alternatif_id`, `kriteria`): satu nilai per kriteria per alternatif |
| created_at, updated_at | timestamp | Waktu rekam |

**`pengaduans`**: id_pengaduan (PK), user_id (FK → users.id, *nullable*), nama_pengadu, email_pengadu, no_hp_pengadu, alamat_pengadu, judul_pengaduan, isi_pengaduan, tanggal_pengaduan, status_pengaduan (enum: Baru, Pending, Dalam Proses, Selesai, Decline), url_lampiran, created_at, updated_at.

**`beritas`**: id_berita (PK), judul_berita, slug (*unique*), kategori (enum: Pengumuman, Kegiatan, Penyaluran Bantuan, Sosialisasi, Informasi Program), penulis (enum: Admin Utama, Editor), status_berita (enum: Draft, Published), gambar_cover, isi_artikel, tanggal_publikasi, created_at, updated_at.

**`galeris`**: id_galeri (PK), judul_kegiatan, slug (*unique*), tgl_pelaksanaan, deskripsi_singkat, created_at, updated_at.
**`galeri_fotos`**: id_foto (PK), galeri_id (FK → galeris.id_galeri, *cascade*), path, urutan, created_at, updated_at.

**`arsips`**: id_arsip (PK), nomor_arsip (*unique*, pola `ARS/001/X/2026`), tgl_dokumen, judul_arsip, klasifikasi, deskripsi_tambahan, lampiran, lampiran_nama, lampiran_ukuran, status_publikasi (enum: Draft, Published), tanggal_publikasi, created_at, updated_at.

> Untuk BAB III, **fokus utama adalah lima tabel inti SPK**: `users`, `pkh_pendaftaran`, `pkh_alternatif`, `pkh_sub_kriteria`, `pkh_penilaian`. Tabel `pengaduans`, `beritas`, `galeris`, `galeri_fotos`, dan `arsips` tetap ditampilkan sebagai tabel pendukung portal, namun dibahas lebih ringkas.

### 5.11 Relasi antar tabel (untuk ERD dan Class Diagram)

- `users` **1 : N** `pkh_pendaftaran` (satu warga dapat memiliki riwayat beberapa pengajuan)
- `users` **1 : 1** `pkh_alternatif` (satu warga hanya sekali menjadi calon penerima — kolom `user_id` bersifat *unique*)
- `pkh_alternatif` **1 : N** `pkh_penilaian` (satu calon dinilai pada lima kriteria)
- `pkh_sub_kriteria` **1 : N** `pkh_penilaian` (satu himpunan dapat dipilih banyak calon)
- `users` **1 : N** `pengaduans`
- `galeris` **1 : N** `galeri_fotos`
- `beritas` dan `arsips` berdiri sendiri (dikelola admin)

### 5.12 Kelas (model, *controller*, *middleware*) untuk Class Diagram

**Model (Eloquent):**

| Kelas | Atribut penting | Metode penting |
|---|---|---|
| `User` | name, nik, email, password, role | `isAdmin()`, `pengaduans()`, `labelRole()`; konstanta `ROLE_LIST` |
| `Pendaftaran` (tabel `pkh_pendaftaran`) | seluruh kolom pendaftaran | `user()`, `sudahDitinjau()`, `badgeStatus()`, `jenisFoto()`; konstanta `PENGHASILAN`, `KONDISI_RUMAH`, `STATUS_PEKERJAAN`, `KEPEMILIKAN_ASET`, `STATUS_LIST`, `DESA`, `FOTO_RUMAH`, `FOTO_KTP` |
| `Alternatif` (tabel `pkh_alternatif`) | user_id, desa | `user()`, `penilaian()`, `nilaiPerKriteria()` |
| `SubKriteria` (tabel `pkh_sub_kriteria`) | kriteria, nama, nilai | `penilaian()` |
| `Penilaian` (tabel `pkh_penilaian`) | alternatif_id, kriteria, sub_kriteria_id | `alternatif()`, `subKriteria()` |
| `Pengaduan` | data pengadu, status_pengaduan | — |
| `Berita` | judul, slug, kategori, status | `scopeTerbit()`, `ringkasan()`, `buatSlug()` |
| `Galeri` / `GaleriFoto` | judul_kegiatan, path, urutan | `fotos()`, `sampul()`, `galeri()` |
| `Arsip` | nomor_arsip, klasifikasi, lampiran | `scopeTerbit()`, `ukuranLampiran()`, `nomorBerikutnya()` |

**Controller:**

| Kelas | Tanggung jawab |
|---|---|
| `AuthController` | Registrasi, masuk, keluar, profil, ubah kata sandi, dasbor |
| `PendaftaranPkhController` | Formulir dan penyimpanan pendaftaran PKH oleh warga |
| `PengaduanController` | Pengaduan warga dan riwayatnya |
| `BeritaController`, `GaleriController` | Halaman publik berita dan galeri |
| `Admin\PendaftaranController` | Daftar & detail pengajuan, tampilkan foto, verifikasi, tolak, hapus |
| `Admin\PkhController` | Konstanta `KRITERIA`, halaman kriteria, CRUD sub-kriteria, perhitungan & perankingan SAW (`hasil()`) |
| `Admin\PkhPenilaianController` | Daftar calon, tambah calon, formulir penilaian, simpan penilaian, hapus calon |
| `Admin\BeritaController`, `Admin\GaleriController`, `Admin\ArsipController`, `Admin\UserController`, `Admin\PengaduanController` | CRUD modul pendukung |

**Middleware:** `EnsureUserIsAdmin` (alias `admin`) memastikan hanya pengguna berperan `admin` yang dapat mengakses rute berawalan `/admin`.

### 5.13 Struktur menu dan daftar halaman (untuk 3.2 dan 3.6)

**Sisi publik / masyarakat:** Beranda (`/`), Tentang (`/tentang`), Berita (`/berita`, detail `/berita/{slug}`), Galeri (`/galeri`, detail `/galeri/{slug}`), Daftar/Masuk (`/register`, `/login`), Dasbor (`/dashboard`), Profil (`/profil`), Pengaduan (`/pengaduan`, riwayat `/pengaduan/riwayat`), Pendaftaran PKH (`/pendaftaran-pkh`).

**Sisi admin (prefiks `/admin`):** Dasbor, Kelola Berita, Kelola Galeri, Kelola Arsip, Kelola Pengguna, Kelola Pengaduan, dan menu **Kelola PKH** yang berisi: Kriteria C1 Penghasilan, C2 Jumlah Tanggungan, C3 Kondisi Rumah, C4 Status Pekerjaan, C5 Kepemilikan Aset → Pendaftaran Masuk → Penilaian Calon → Hasil Akhir.

**Berkas tampilan (Blade) yang tersedia:** `Pengguna/HalamanUtama/home`, `Pengguna/tentangs/tentang`, `Pengguna/Beritas/{index,show}`, `Pengguna/Galeris/{index,show}`, `Pengguna/PendaftaranPkh/daftar`, `Pengguna/Pengaduans/{pengaduan,riwayat}`, `auth/{login,register}`, `profile/profile`, `admin/dashboard`, `admin/kelola_pkh/{kriteria,pendaftaran_index,pendaftaran_show,penilaian,penilaian_nilai,hasil}`, `admin/kelola_berita/*`, `admin/kelola_galeri/*`, `admin/kelola_arsip/*`, `admin/kelola_user/*`, `admin/kelola_pengaduan/*`.

**Tema antarmuka:** nuansa pemerintahan — biru navy `#0E2650` dan `#14346B`, aksen merah `#C8102E`, latar putih/abu muda; tata letak admin memakai *sidebar* kiri dengan menu *dropdown* Kelola PKH.

---

## 6. INSTRUKSI ISI TIAP SUB-BAB

Tulis paragraf pembuka BAB III (2–3 kalimat) sebelum sub-bab 3.1 yang menyatakan bahwa bab ini menguraikan analisis sistem berjalan, sistem yang diajukan, penerapan metode SAW, serta perancangan sistem, basis data, dan antarmuka.

**3.1 Analisis** — paragraf pengantar singkat (3–4 kalimat) tentang tujuan tahap analisis pada model *Waterfall*.

**3.1.1 Analisis Sistem yang Sedang Berjalan** — 3–4 paragraf berdasarkan Bagian 5.3. Sertakan **Gambar 3. 1 Flowchart Sistem yang Sedang Berjalan** dan satu tabel identifikasi masalah beserta dampaknya (**Tabel 3. 1**). Gunakan simbol *flowchart* yang sudah dijelaskan di BAB II.

**3.1.2 Analisis Sistem yang Diajukan** — 4–5 paragraf berdasarkan Bagian 5.4, ditutup dengan **Gambar 3. 2 Flowchart Sistem yang Diajukan**. Tambahkan analisis kebutuhan dalam dua tabel: **Tabel 3. 2 Kebutuhan Fungsional** (kode SRS-F01 dst., lengkap dengan aktor pelaksana) dan **Tabel 3. 3 Kebutuhan Non-Fungsional** (keamanan berkas foto pada *disk* privat, pembatasan akses berdasarkan peran, validasi masukan, kemudahan penggunaan, kompatibilitas peramban). Sebutkan pula kebutuhan perangkat keras/lunak minimum secara wajar.

**3.1.3 Analisis Penerapan Metode SAW** — bagian terpenting. Urutan penulisan:
1. Langkah metode SAW secara ringkas (mengacu penjelasan BAB II, jangan menyalin ulang seluruhnya).
2. **Tabel 3. 4 Kriteria dan Bobot** (Bagian 5.5) + penjelasan mengapa seluruh kriteria berjenis *benefit* dan mengapa kriteria disimpan sebagai konstanta di kode.
3. **Tabel 3. 5 Sub-kriteria dan Nilai *Crisp*** (Bagian 5.6).
4. **Tabel 3. 6 Matriks Keputusan (X)**, **Tabel 3. 7 Matriks Ternormalisasi (R)**, **Tabel 3. 8 Nilai Preferensi dan Perankingan** — pakai angka pada Bagian 5.8 apa adanya, tampilkan minimal dua contoh rincian perhitungan `V_i` dalam bentuk persamaan bertahap.
5. Penjelasan **perankingan per desa** beserta contoh Desa Pasar Bantal, termasuk alasan perancangan: penyaringan desa dilakukan sebelum normalisasi sehingga nilai maksimum kolom diambil hanya dari calon di desa tersebut, dan pemerataan antar desa menjadi lebih adil.
6. Penegasan bahwa keluaran sistem bersifat rekomendasi.

**3.2 Rancangan Struktur Sistem yang Akan Dibangun** — uraikan struktur modul/menu sistem, sertakan **Gambar 3. 3 Struktur Menu Sistem** (diagram pohon: cabang publik/masyarakat dan cabang admin) berdasarkan Bagian 5.13, ditambah tabel ringkas modul dan fungsinya bila diperlukan.

**3.3 Rancangan Sistem** — paragraf pengantar bahwa perancangan dimodelkan dengan UML [12].

**3.3.1 Rancangan Arsitektur** — jelaskan arsitektur klien-server tiga lapis dan pola MVC Laravel: peramban mengirim permintaan HTTP → *routing* → *middleware* (`auth`, `admin`) → *controller* → *model* Eloquent → basis data MySQL → tampilan Blade dikembalikan ke peramban; berkas foto disimpan pada *disk* privat dan hanya dapat diakses melalui rute khusus admin. Sertakan **Gambar 3. 4 Rancangan Arsitektur Sistem**.

**3.3.2 Use Case Diagram** — **Gambar 3. 5 Use Case Diagram**, tiga aktor sesuai Bagian 5.2. Lengkapi dengan **Tabel 3. 9 Deskripsi *Use Case*** (nama *use case*, aktor, deskripsi singkat). Tampilkan relasi `<<include>>` untuk "Verifikasi Pengajuan" yang selalu memanggil "Tambah Calon Penerima", dan `<<extend>>` untuk "Saring per Desa" terhadap "Lihat Hasil Akhir".

**3.3.3 Class Diagram** — **Gambar 3. 6 Class Diagram**, berisi kelas model beserta atribut dan metode utama (Bagian 5.12) serta relasi antar kelas dengan multiplisitas. Boleh menyertakan lapisan *controller* utama modul SPK agar pola MVC terlihat. Tambahkan tabel penjelasan kelas bila membantu.

**3.3.4 Sequence Diagram** — buat **empat** diagram beserta penjelasan alurnya masing-masing:
- **Gambar 3. 7** Pendaftaran PKH oleh Masyarakat
- **Gambar 3. 8** Verifikasi Pengajuan oleh Admin
- **Gambar 3. 9** Penilaian Calon Penerima
- **Gambar 3. 10** Perhitungan dan Perankingan SAW pada Hasil Akhir
Objek yang dilibatkan: Aktor → Halaman Blade → *Route* → *Controller* → *Model* → Basis Data, dengan pesan balik ke tampilan.

**3.3.5 Activity Diagram** — buat **empat** diagram dengan *swimlane* (Masyarakat | Sistem | Admin):
- **Gambar 3. 11** Pendaftaran PKH
- **Gambar 3. 12** Verifikasi Pengajuan
- **Gambar 3. 13** Penilaian Calon Penerima
- **Gambar 3. 14** Perhitungan Hasil Akhir SAW
Sertakan percabangan keputusan yang nyata: validasi formulir gagal/berhasil, pengajuan ganda ditolak, pilihan verifikasi/tolak, serta pemisahan calon yang penilaiannya belum lengkap.

**3.4 Entity Relationship Diagram (ERD)** — **Gambar 3. 15 ERD Sistem**, gambarkan entitas, atribut kunci, dan kardinalitas sesuai Bagian 5.11. Jelaskan tiap relasi dalam bentuk kalimat, dan tegaskan bahwa kriteria C1–C5 tidak menjadi entitas karena disimpan sebagai konstanta di dalam kode, sedangkan himpunannya menjadi entitas `pkh_sub_kriteria`.

**3.5 Perancangan Basis Data** — nama basis data dan penjelasan singkat, lalu **satu tabel struktur untuk setiap tabel** dengan kolom: Nama Field, Tipe Data, Panjang, Keterangan (PK/FK/*unique*/*nullable*). Gunakan Bagian 5.10 apa adanya. Urutan penomoran: **Tabel 3. 10** `users`, **Tabel 3. 11** `pkh_pendaftaran`, **Tabel 3. 12** `pkh_alternatif`, **Tabel 3. 13** `pkh_sub_kriteria`, **Tabel 3. 14** `pkh_penilaian`, lalu tabel pendukung **Tabel 3. 15** `pengaduans`, **Tabel 3. 16** `beritas`, **Tabel 3. 17** `galeris`, **Tabel 3. 18** `galeri_fotos`, **Tabel 3. 19** `arsips`.

**3.6 Perancangan Antarmuka (Interface)** — rancangan antarmuka dalam bentuk *wireframe* / sketsa tata letak beserta penjelasan tiap bagian layar. Minimal sepuluh rancangan: Beranda, Masuk, Daftar Akun, Formulir Pendaftaran PKH (termasuk area unggah 5 foto), Riwayat/Status Pengajuan pada dasbor warga, Dasbor Admin, Pendaftaran Masuk (daftar + penyaring), Detail Pengajuan (data + galeri foto + tombol verifikasi/tolak), Kelola Kriteria & Sub-kriteria, Penilaian Calon, Formulir Penilaian (dengan panel acuan *self-report* dan foto), serta Hasil Akhir (tabel matriks, normalisasi, nilai preferensi, peringkat, penyaring desa). Beri nomor **Gambar 3. 16** dan seterusnya, sebutkan skema warna tema pemerintahan pada Bagian 5.13.

Tutup BAB III dengan satu paragraf ringkas yang menyatakan bahwa hasil perancangan pada bab ini menjadi dasar tahap implementasi dan pengujian pada BAB IV.

---

## 7. FORMAT KELUARAN YANG DIMINTA

1. Tulis dalam **Markdown** dengan heading yang jelas, agar mudah ditempel ke Word lalu diberi gaya *Heading*.
2. Seluruh tabel ditulis sebagai tabel Markdown, dengan judul tabel di baris tersendiri **di atas** tabel, contoh: `Tabel 3. 1 Identifikasi Masalah pada Sistem yang Sedang Berjalan`.
3. Untuk setiap gambar/diagram, tulis dengan pola:
   - kalimat pengantar yang merujuk gambar;
   - blok kode berisi **PlantUML** (untuk *use case*, *class*, *sequence*, *activity*, ERD) atau **Mermaid** (untuk *flowchart* dan struktur menu) agar dapat langsung digambar oleh penulis;
   - untuk *wireframe* antarmuka, gunakan sketsa ASCII di dalam blok kode;
   - keterangan gambar di bawahnya, contoh: `Gambar 3. 5 Use Case Diagram Sistem`;
   - 1–2 paragraf penjelasan isi gambar.
4. Angka desimal ditulis dengan **koma** (0,30 bukan 0.30) pada teks laporan.
5. Panjang wajar keseluruhan: setara 25–35 halaman A4 (bab perancangan memang panjang karena banyak tabel dan gambar).
6. Di bagian paling akhir keluaran, tambahkan (di luar isi bab, diberi garis pemisah):
   - daftar seluruh **Tabel 3.x** dan **Gambar 3.x** yang dihasilkan, untuk memudahkan pembuatan Daftar Tabel dan Daftar Gambar;
   - catatan konfirmasi mengenai sub-bab 3.1.3;
   - daftar hal yang masih perlu dilengkapi penulis (mis. tangkapan layar asli untuk BAB IV, nama basis data sesungguhnya bila berbeda).

---

## 8. LARANGAN

1. Dilarang menambah atau mengubah kriteria, bobot, sub-kriteria, nilai *crisp*, nama tabel, nama kolom, atau fitur di luar Bagian 5.
2. Dilarang membuat referensi/daftar pustaka baru atau memakai nomor sitasi di luar `[1]`–`[14]` yang sudah ada.
3. Dilarang memakai kata ganti orang dan kalimat aktif berorientasi penulis.
4. Dilarang membahas hasil pengujian *black box*, tangkapan layar sistem jadi, kesimpulan, atau saran — semuanya milik BAB IV dan BAB V.
5. Dilarang mengubah urutan dan penomoran sub-bab pada Bagian 4.
6. Dilarang menghitung ulang contoh SAW dengan hasil yang berbeda dari Bagian 5.8.
7. Dilarang menyebut nama *file* kode, *namespace*, atau potongan kode PHP di dalam narasi bab; cukup istilah perancangan (kelas, model, *controller*) seperlunya pada 3.3.3.

---

## 9. CHECKLIST MUTU SEBELUM MENGIRIM JAWABAN

- [ ] Seluruh sub-bab 3.1 s.d. 3.6 lengkap sesuai urutan panduan.
- [ ] Setiap tabel dan gambar bernomor urut tanpa lompatan dan sudah dirujuk di dalam kalimat.
- [ ] Bobot kriteria berjumlah 1,00 dan konsisten di seluruh bab.
- [ ] Nilai preferensi dan urutan peringkat sama persis dengan Bagian 5.8.
- [ ] Alur pendaftaran → verifikasi → penilaian → hasil akhir konsisten di *flowchart*, *sequence*, dan *activity diagram*.
- [ ] Semua entitas pada ERD punya tabel struktur padanannya di sub-bab 3.5.
- [ ] Tidak ada kata ganti orang; istilah asing dicetak miring.
- [ ] Tidak ada fitur, tabel, atau kolom yang tidak tercantum pada Bagian 5.
