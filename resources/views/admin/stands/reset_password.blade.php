@extends('layouts.admin')

@section('title', 'Reset Password Vendor - Admin Kantin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Reset Password Vendor')
@section('page-subtitle', 'Ubah kata sandi akun login pemilik stand yang bersangkutan')

@section('sidebar-nav')
<a href="{{ route('admin.dashboard') }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
    Dashboard
</a>

<div class="pt-3 pb-1 px-2">
    <span class="text-[10px] uppercase tracking-widest font-bold text-slate-500">Manajemen</span>
</div>

<a href="{{ route('admin.stands.create') }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
    Tambah Stand Baru
</a>

<a href="{{ route('katalog.index') }}" target="_blank" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
    Katalog Siswa
</a>
@endsection

@section('content')
<div class="p-4 sm:p-6 max-w-xl">

    <div class="mb-4">
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors inline-flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Dashboard Admin</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-lg font-black text-slate-900">Reset Kata Sandi Vendor</h1>
            <p class="text-xs text-slate-500 mt-1">Ubah kata sandi jika penjual stand lupa password akses panel.</p>
        </div>

        <!-- Vendor Info Pill -->
        <div class="p-3.5 rounded-2xl bg-orange-50 border border-orange-200/80 mb-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-500 text-white font-black text-sm flex items-center justify-center shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-black text-slate-900 truncate">{{ $user->name }}</p>
                <p class="text-[11px] text-slate-600 truncate">{{ $user->email }}</p>
                @if($user->stand)
                <p class="text-[10px] font-bold text-orange-700 mt-0.5">{{ $user->stand->nama_stand }} ({{ $user->stand->nomor_stand }})</p>
                @endif
            </div>
        </div>

        @if($errors->any())
        <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form action="{{ route('admin.vendors.doResetPassword', $user) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Baru *</label>
                <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Ulangi Kata Sandi Baru *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Konfirmasi kata sandi baru"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs shadow-md shadow-orange-500/25 transition-all cursor-pointer">
                    Simpan Sandi Baru
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
