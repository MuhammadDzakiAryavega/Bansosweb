<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class KelolaUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->withCount('pengaduans');

        if ($request->filled('role') && in_array($request->role, User::ROLE_LIST, true)) {
            $query->where('role', $request->role);
        }

        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%");
            });
        }

        $users = $query->latest('created_at')->paginate(10)->withQueryString();

        $statistik = [
            'total'      => User::count(),
            'admin'      => User::where('role', 'admin')->count(),
            'seksi'      => User::where('role', 'seksi')->count(),
            'masyarakat' => User::where('role', 'user')->count(),
        ];

        return view('admin.kelola_user.index', [
            'users'     => $users,
            'statistik' => $statistik,
            'roleList'  => User::ROLE_LIST,
        ]);
    }

    public function create()
    {
        return view('admin.kelola_user.create', [
            'roleList' => User::ROLE_LIST,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        User::create($data);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.kelola_user.edit', [
            'user'     => $user->loadCount('pengaduans'),
            'roleList' => User::ROLE_LIST,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validasi($request, $user);

        // Kata sandi hanya diganti bila diisi.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Admin tidak boleh menurunkan perannya sendiri agar tidak kehilangan akses panel.
        if ($user->is(Auth::user()) && $data['role'] !== 'admin') {
            return back()
                ->withInput()
                ->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
        }

        // Sisakan minimal satu administrator aktif.
        if ($user->isAdmin() && $data['role'] !== 'admin' && $this->jumlahAdmin() <= 1) {
            return back()
                ->withInput()
                ->with('error', 'Peran tidak dapat diubah karena ini satu-satunya akun administrator.');
        }

        $user->update($data);

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->is(Auth::user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->isAdmin() && $this->jumlahAdmin() <= 1) {
            return back()->with('error', 'Akun ini tidak dapat dihapus karena satu-satunya administrator.');
        }

        $user->delete();

        return redirect()
            ->route('admin.user.index')
            ->with('success', 'Akun pengguna berhasil dihapus.');
    }

    /* ---------- Pembantu ---------- */

    private function validasi(Request $request, ?User $user = null): array
    {
        $wajibSandi = $user ? 'nullable' : 'required';

        return $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'nik'      => ['required', 'digits:16', Rule::unique('users', 'nik')->ignore($user)],
            'email'    => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user)],
            'role'     => ['required', Rule::in(User::ROLE_LIST)],
            'password' => [$wajibSandi, 'confirmed', 'min:8', 'max:20'],
        ], [
            'required'           => ':attribute wajib diisi.',
            'name.max'           => 'Nama lengkap maksimal 100 karakter.',
            'nik.digits'         => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'         => 'NIK ini sudah terdaftar pada portal.',
            'email.email'        => 'Format alamat surel tidak valid.',
            'email.max'          => 'Alamat surel maksimal 100 karakter.',
            'email.unique'       => 'Alamat surel ini sudah terdaftar pada portal.',
            'role.in'            => 'Pilihan peran tidak valid.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.max'       => 'Kata sandi maksimal 20 karakter.',
        ], [
            'name'     => 'Nama lengkap',
            'nik'      => 'NIK',
            'email'    => 'Alamat surel',
            'role'     => 'Peran',
            'password' => 'Kata sandi',
        ]);
    }

    private function jumlahAdmin(): int
    {
        return User::where('role', 'admin')->count();
    }
}
