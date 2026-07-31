@extends('layouts.admin')

@section('title', 'Ubah Pengguna - Admin Portal PKH')
@section('page-title', 'Ubah Pengguna')
@section('page-subtitle', 'Perbarui data diri, peran, atau kata sandi akun pengguna.')

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.user.index') }}"
           class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-[#14346B] transition-colors">
            <i class="fas fa-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Daftar Pengguna
        </a>

        <div class="flex items-center gap-4 text-xs text-slate-500">
            <span>
                <i class="far fa-clock mr-1.5"></i>
                Terdaftar {{ $user->created_at->translatedFormat('d F Y, H:i') }} WIB
            </span>
            <span class="text-[11px] font-semibold px-3 py-1.5 rounded-md border {{ match ($user->role) {
                'admin' => 'bg-[#C8102E]/5 text-[#C8102E] border-[#C8102E]/20',
                'seksi' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                default => 'bg-[#14346B]/5 text-[#14346B] border-[#14346B]/20',
            } }}">
                {{ $user->labelRole() }}
            </span>
        </div>
    </div>

    @include('admin.kelola_user.form', [
        'user'        => $user,
        'aksi'        => route('admin.user.update', $user->id),
        'metode'      => 'PUT',
        'labelTombol' => 'Perbarui Pengguna',
    ])

@endsection
