<?php

namespace App\Http\Controllers;

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
            'name'     => ['required', 'string', 'max:255'],
            'nik'      => ['required', 'digits:16', 'unique:users,nik'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'required'        => ':attribute wajib diisi.',
            'name.max'        => 'Nama lengkap maksimal 255 karakter.',
            'nik.digits'      => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'      => 'NIK ini sudah terdaftar pada portal.',
            'email.email'     => 'Format alamat surel tidak valid.',
            'email.max'       => 'Alamat surel maksimal 255 karakter.',
            'email.unique'    => 'Alamat surel ini sudah terdaftar pada portal.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min'    => 'Kata sandi minimal 8 karakter.',
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

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('dashboard'));
            }

            return redirect()->route('home');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    /* ---------- DASHBOARD ---------- */
    public function dashboard()
    {
        if (! Auth::user()->isAdmin()) {
            return redirect()->route('home');
        }

        return view('admin.dashboard', [
            'statistik' => [
                'pengguna'       => User::count(),
                'pengaduan'      => Pengaduan::count(),
                'perlu_ditindak' => Pengaduan::whereIn('status_pengaduan', ['Baru', 'Pending', 'Dalam Proses'])->count(),
                'selesai'        => Pengaduan::where('status_pengaduan', 'Selesai')->count(),
            ],
            'pengaduanTerbaru' => Pengaduan::latest('tanggal_pengaduan')->take(5)->get(),
        ]);
    }

    /* ---------- PROFIL ---------- */
    public function profile()
    {
        return view('profile.profile');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max'      => 'Nama lengkap maksimal 255 karakter.',
        ]);

        $request->user()->update($data);

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'          => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.required'         => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required'                 => 'Kata sandi baru wajib diisi.',
            'password.confirmed'                => 'Konfirmasi kata sandi baru tidak sama.',
            'password.min'                      => 'Kata sandi baru minimal 8 karakter.',
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