@extends('layouts.admin')

@section('title', 'Reset Password Vendor - Admin Kantin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Reset Password Vendor')
@section('page-subtitle', 'Ubah kata sandi akun login pemilik stand yang bersangkutan')

@section('sidebar-nav')
    @include('admin.partials.sidebar_nav')
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
