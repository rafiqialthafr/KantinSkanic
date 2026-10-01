@extends('layouts.admin')

@section('title', 'Tambah Stand Baru - Admin Kantin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Tambah Stand Baru')
@section('page-subtitle', 'Buat stand kantin baru beserta akun login untuk penjual vendor')

@section('sidebar-nav')
    @include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-4 sm:p-6 max-w-3xl">

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
            <h1 class="text-xl font-black text-slate-900">Form Pendaftaran Stand & Akun Vendor</h1>
            <p class="text-xs text-slate-500 mt-1">Sistem akan secara atomik (DB Transaction) membuat profil stand sekaligus akun user dengan role penjual.</p>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <p class="font-bold mb-1">Terdapat kesalahan pengisian:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.stands.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Data Stand -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-orange-600 mb-3">1. Data Fisik Stand</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_stand" class="block text-xs font-bold text-slate-700 mb-1">Nama Stand *</label>
                        <input type="text" id="nama_stand" name="nama_stand" value="{{ old('nama_stand') }}" required placeholder="Contoh: Kantin Bu Siti"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>
                    <div>
                        <label for="nomor_stand" class="block text-xs font-bold text-slate-700 mb-1">Nomor / Kode Stand *</label>
                        <input type="text" id="nomor_stand" name="nomor_stand" value="{{ old('nomor_stand') }}" required placeholder="Contoh: Stand 06"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>
                    <div>
                        <label for="pemilik" class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik Stand *</label>
                        <input type="text" id="pemilik" name="pemilik" value="{{ old('pemilik') }}" required placeholder="Contoh: Ibu Siti Rahayu"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>
                    <div>
                        <label for="no_wa" class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp Pemilik *</label>
                        <input type="text" id="no_wa" name="no_wa" value="{{ old('no_wa') }}" required placeholder="Contoh: 081234567890"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>
                </div>

                <div class="mt-4">
                    <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat / Menu Andalan</label>
                    <textarea id="deskripsi" name="deskripsi" rows="2" placeholder="Contoh: Spesialis Bakso Sapi Asli & Aneka Minuman Dingin"
                              class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <!-- Section 2: Kredensial Login Vendor -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-orange-600 mb-3">2. Akun Login Penjual (Vendor Credential)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email Login Vendor *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="busiti@kantin.com"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                        <span class="text-[10px] text-slate-400 mt-1 block">Digunakan vendor untuk login di /login</span>
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password Sementara *</label>
                        <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                        <span class="text-[10px] text-slate-400 mt-1 block">Berikan kata sandi ini kepada pemilik stand</span>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-black text-xs sm:text-sm shadow-md shadow-orange-500/25 transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>Simpan Stand & Akun Vendor</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
