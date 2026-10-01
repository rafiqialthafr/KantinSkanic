@extends('layouts.app')

@section('title', 'Daftar Akun Siswa - Pre-Order Kantin Skanic')

@section('content')
<div class="max-w-md mx-auto px-4 py-8 sm:py-12">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 p-6 sm:p-8">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-orange-500/20 mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.765Z" />
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900">Daftar Akun Siswa</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Buat akun cepat untuk menyelesaikan pesanan pre-order</p>
        </div>

        @if($errors->any())
        <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <p class="font-bold mb-1">Pendaftaran belum berhasil:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap Siswa *</label>
                <div class="relative">
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Ahmad Rizki Pratama"
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
            </div>

            <!-- Kelas & No WA Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="kelas" class="block text-xs font-bold text-slate-700 mb-1.5">Kelas *</label>
                    <input type="text" id="kelas" name="kelas" value="{{ old('kelas') }}" required placeholder="XI PPLG 1"
                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                </div>
                <div>
                    <label for="no_wa" class="block text-xs font-bold text-slate-700 mb-1.5">No. WhatsApp *</label>
                    <input type="tel" id="no_wa" name="no_wa" value="{{ old('no_wa') }}" required placeholder="081234567890"
                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email *</label>
                <div class="relative">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="siswa@smkn1ciomas.sch.id"
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
            </div>

            <!-- Password & Konfirmasi Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi *</label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter"
                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Ulangi Kata Sandi *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi sandi"
                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                </div>
            </div>

            <button type="submit"
                    class="w-full mt-2 py-3.5 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 active:scale-98 text-white font-extrabold text-sm shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Daftar & Lanjutkan Pesanan</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:underline">Masuk di sini</a>
            </p>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('katalog.index') }}" class="text-xs text-slate-400 hover:text-slate-600 font-semibold hover:underline">
                &larr; Kembali ke Katalog Menu
            </a>
        </div>
    </div>
</div>
@endsection
