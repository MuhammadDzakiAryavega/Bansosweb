<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Pendaftaran;
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /* ---------- REGISTER ---------- */
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'nik'      => ['required', 'digits:16', 'unique:users,nik'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8', 'max:20'],
        ], [
            'required'        => ':attribute wajib diisi.',
            'name.max'        => 'Nama lengkap maksimal 100 karakter.',
            'nik.digits'      => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'      => 'NIK ini sudah terdaftar pada portal.',
            'email.email'     => 'Format alamat surel tidak valid.',
            'email.max'       => 'Alamat surel maksimal 100 karakter.',
            'email.unique'    => 'Alamat surel ini sudah terdaftar pada portal.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min'    => 'Kata sandi minimal 8 karakter.',
            'password.max'    => 'Kata sandi maksimal 20 karakter.',
        ], [
            'name'     => 'Nama lengkap',
            'nik'      => 'NIK',
            'email'    => 'Alamat surel',
            'password' => 'Kata sandi',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'nik'      => $data['nik'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'role'     => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    /* ---------- LOGIN ---------- */
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'required'    => ':attribute wajib diisi.',
            'email.email' => 'Format alamat surel tidak valid.',
        ], [
            'email'    => 'Alamat surel',
            'password' => 'Kata sandi',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->to($this->tujuanSetelahLogin($request));
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    /**
     * Tujuan setelah login berhasil.
     *
     * Alamat yang sempat dituju saat masih tamu (url.intended) hanya dipakai
     * bila sesuai peran. Tanpa penyaringan ini, admin yang tadinya menekan menu
     * "Pengaduan" di navbar publik akan mendarat di halaman pengguna, bukan dashboard.
     */
    private function tujuanSetelahLogin(Request $request): string
    {
        $tujuan = $request->session()->pull('url.intended');
        $adalahPetugas = Auth::user()->isPetugas();

        $keAreaAdmin = $tujuan
            && str_starts_with(ltrim((string) parse_url($tujuan, PHP_URL_PATH), '/'), 'admin');

        if ($tujuan && $keAreaAdmin === $adalahPetugas) {
            return $tujuan;
        }

        return $adalahPetugas ? route('dashboard') : route('home');
    }

    /* ---------- DASHBOARD ---------- */

    /**
     * Dashboard panel dipakai bersama admin & seksi, tetapi isinya mengikuti
     * kewenangan masing-masing (lihat pembagian rute di routes/web.php) agar
     * tidak ada pintasan ke modul yang justru ditolak 403 saat diklik.
     */
    public function dashboard()
    {
        $user = Auth::user();

        if (! $user->isPetugas()) {
            return redirect()->route('home');
        }

        return view('admin.dashboard', $user->isAdmin()
            ? $this->dashboardAdmin()
            : $this->dashboardSeksi());
    }

    /** Data dashboard administrator: konten portal & akun pengguna. */
    private function dashboardAdmin(): array
    {
        $draf = Berita::where('status_berita', 'Draft')->count();

        return [
            'pengantar' => 'Kelola kriteria penilaian PKH, berita, dokumentasi kegiatan, serta akun pengguna '
                . 'Portal PKH. Konten yang berstatus Published langsung tampil pada halaman publik.',
            'sorotan' => [
                'label'  => 'Berita draf',
                'nilai'  => $draf . ' berita',
                'accent' => $draf > 0,
            ],
            'tombolUtama' => [
                ['label' => 'Kelola Berita', 'icon' => 'fa-newspaper', 'url' => route('admin.berita.index'), 'utama' => true],
                ['label' => 'Kelola Pengguna', 'icon' => 'fa-users', 'url' => route('admin.user.index'), 'utama' => false],
            ],
            'kartuStatistik' => [
                ['label' => 'Total Akun', 'value' => User::count(), 'desc' => 'Seluruh akun terdaftar', 'icon' => 'fa-users'],
                ['label' => 'Akun Masyarakat', 'value' => User::where('role', 'user')->count(), 'desc' => 'Warga pengguna portal', 'icon' => 'fa-user'],
                ['label' => 'Berita Terbit', 'value' => Berita::where('status_berita', 'Published')->count(), 'desc' => 'Tampil di halaman publik', 'icon' => 'fa-newspaper'],
                ['label' => 'Album Galeri', 'value' => Galeri::count(), 'desc' => 'Dokumentasi kegiatan', 'icon' => 'fa-images'],
            ],
            'panelTerbaru' => [
                'kicker'     => 'Konten Terkini',
                'judul'      => 'Berita Terbaru',
                'lihatSemua' => route('admin.berita.index'),
                'kosongJudul' => 'Belum ada berita',
                'kosongPesan' => 'Berita yang Anda tulis akan tampil di sini.',
                'kosongIcon'  => 'fa-newspaper',
                'items'      => Berita::latest('created_at')->take(5)->get()->map(fn (Berita $b) => [
                    'inisial'    => strtoupper(substr($b->judul_berita, 0, 1)),
                    'judul'      => $b->judul_berita,
                    'subjudul'   => $b->kategori . ' · ' . $b->created_at->translatedFormat('d M Y'),
                    'badge'      => $b->status_berita,
                    'badgeKelas' => $b->status_berita === 'Published'
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        : 'bg-slate-50 text-slate-600 border-slate-200',
                    'url'        => route('admin.berita.edit', $b->id_berita),
                ])->all(),
            ],
            'aksesCepat' => [
                ['icon' => 'fa-sliders', 'title' => 'Kelola Kriteria', 'desc' => 'Atur sub-kriteria C1&ndash;C5 beserta nilainya.', 'link' => route('admin.pkh.kriteria')],
                ['icon' => 'fa-newspaper', 'title' => 'Kelola Berita', 'desc' => 'Tulis &amp; publikasikan informasi program.', 'link' => route('admin.berita.index')],
                ['icon' => 'fa-images', 'title' => 'Kelola Galeri', 'desc' => 'Unggah &amp; tata dokumentasi kegiatan.', 'link' => route('admin.galeri.index')],
                ['icon' => 'fa-users', 'title' => 'Kelola Pengguna', 'desc' => 'Atur akun masyarakat, admin &amp; seksi.', 'link' => route('admin.user.index')],
            ],
        ];
    }

    /** Data dashboard seksi: proses PKH, pengaduan, dan arsip. */
    private function dashboardSeksi(): array
    {
        $perluDitindak = Pengaduan::whereIn('status_pengaduan', ['Baru', 'Pending', 'Dalam Proses'])->count();

        return [
            'pengantar' => 'Tinjau pengajuan PKH yang masuk, nilai calon penerima berdasarkan kriteria, '
                . 'lalu lihat perankingan SAW. Tindak lanjuti pula pengaduan masyarakat dan arsip dokumen.',
            'sorotan' => [
                'label'  => 'Perlu ditindak',
                'nilai'  => $perluDitindak . ' pengaduan',
                'accent' => $perluDitindak > 0,
            ],
            'tombolUtama' => [
                ['label' => 'Pendaftaran Masuk', 'icon' => 'fa-inbox', 'url' => route('admin.pkh.pendaftaran.index'), 'utama' => true],
                ['label' => 'Hasil Akhir SAW', 'icon' => 'fa-trophy', 'url' => route('admin.pkh.hasil'), 'utama' => false],
            ],
            'kartuStatistik' => [
                ['label' => 'Pengajuan Baru', 'value' => Pendaftaran::where('status', 'Baru')->count(), 'desc' => 'Menunggu ditinjau', 'icon' => 'fa-inbox', 'accent' => true],
                ['label' => 'Calon Penerima', 'value' => Alternatif::count(), 'desc' => 'Alternatif yang dinilai', 'icon' => 'fa-clipboard-check'],
                ['label' => 'Total Pengaduan', 'value' => Pengaduan::count(), 'desc' => 'Laporan masuk seluruhnya', 'icon' => 'fa-bullhorn'],
                ['label' => 'Perlu Ditindak', 'value' => $perluDitindak, 'desc' => 'Baru, pending & dalam proses', 'icon' => 'fa-triangle-exclamation', 'accent' => true],
            ],
            'panelTerbaru' => [
                'kicker'      => 'Tindak Lanjut',
                'judul'       => 'Pengaduan Terbaru',
                'lihatSemua'  => route('admin.pengaduan.index'),
                'kosongJudul' => 'Belum ada pengaduan masuk',
                'kosongPesan' => 'Pengaduan dari masyarakat akan tampil di sini.',
                'kosongIcon'  => 'fa-inbox',
                'items'       => Pengaduan::latest('tanggal_pengaduan')->take(5)->get()->map(fn (Pengaduan $p) => [
                    'inisial'    => strtoupper(substr($p->nama_pengadu, 0, 1)),
                    'judul'      => $p->judul_pengaduan,
                    'subjudul'   => $p->nama_pengadu . ' · ' . $p->tanggal_pengaduan->translatedFormat('d M Y, H:i') . ' WIB',
                    'badge'      => $p->status_pengaduan,
                    'badgeKelas' => match ($p->status_pengaduan) {
                        'Baru'         => 'bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20',
                        'Pending'      => 'bg-amber-50 text-amber-700 border-amber-200',
                        'Dalam Proses' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'Selesai'      => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'Decline'      => 'bg-rose-50 text-[#C8102E] border-rose-200',
                        default        => 'bg-slate-50 text-slate-600 border-slate-200',
                    },
                    'url'        => route('admin.pengaduan.show', $p->id_pengaduan),
                ])->all(),
            ],
            'aksesCepat' => [
                ['icon' => 'fa-inbox', 'title' => 'Pendaftaran Masuk', 'desc' => 'Verifikasi atau tolak pengajuan warga.', 'link' => route('admin.pkh.pendaftaran.index')],
                ['icon' => 'fa-clipboard-check', 'title' => 'Penilaian Calon', 'desc' => 'Isi nilai tiap kriteria calon penerima.', 'link' => route('admin.pkh.penilaian.index')],
                ['icon' => 'fa-trophy', 'title' => 'Hasil Akhir', 'desc' => 'Perankingan SAW, dapat disaring per desa.', 'link' => route('admin.pkh.hasil')],
                ['icon' => 'fa-bullhorn', 'title' => 'Kelola Pengaduan', 'desc' => 'Tinjau laporan &amp; ubah status tindak lanjut.', 'link' => route('admin.pengaduan.index')],
                ['icon' => 'fa-folder-open', 'title' => 'Kelola Arsip', 'desc' => 'Catat surat masuk, keluar &amp; dokumen penting.', 'link' => route('admin.arsip.index')],
            ],
        ];
    }

    /* ---------- PROFIL (khusus pengguna, petugas diarahkan ke dashboard) ---------- */
    public function profile()
    {
        if (Auth::user()->isPetugas()) {
            return redirect()->route('dashboard');
        }

        return view('profile.profile');
    }

    public function updateProfile(Request $request)
    {
        if ($request->user()->isPetugas()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max'      => 'Nama lengkap maksimal 100 karakter.',
        ]);

        $request->user()->update($data);

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        if ($request->user()->isPetugas()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'          => ['required', 'confirmed', 'min:8', 'max:20'],
        ], [
            'current_password.required'         => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required'                 => 'Kata sandi baru wajib diisi.',
            'password.confirmed'                => 'Konfirmasi kata sandi baru tidak sama.',
            'password.min'                      => 'Kata sandi baru minimal 8 karakter.',
            'password.max'                      => 'Kata sandi baru maksimal 20 karakter.',
        ]);

        $request->user()->update([
            'password' => $data['password'],
        ]);

        return back()->with('status', 'password-updated');
    }

    /* ---------- LOGOUT ---------- */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}