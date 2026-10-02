@extends('layouts.admin')

@section('title', 'Edit Stand - Admin Kantin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Edit Stand')
@section('page-subtitle', 'Perbarui data stand kantin')

@section('sidebar-nav')
    @include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-4 sm:p-6 max-w-3xl">

    <div class="mb-4">
        <a href="{{ route('admin.stands.index') }}" class="text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors inline-flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Kelola Stand</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-xl font-black text-slate-900">Edit Data Stand</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui informasi stand. Untuk mengubah email/password vendor, gunakan fitur Reset Password.</p>
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

        <form action="{{ route('admin.stands.update', $stand) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-orange-600 mb-3">1. Data Fisik Stand</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_stand" class="block text-xs font-bold text-slate-700 mb-1">Nama Stand *</label>
                        <input type="text" id="nama_stand" name="nama_stand" value="{{ old('nama_stand', $stand->nama_stand) }}" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>
                    <div>
                        <label for="nomor_stand" class="block text-xs font-bold text-slate-700 mb-1">Nomor / Kode Stand *</label>
                        <input type="text" id="nomor_stand" name="nomor_stand" value="{{ old('nomor_stand', $stand->nomor_stand) }}" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>
                    <div>
                        <label for="pemilik" class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik Stand *</label>
                        <input type="text" id="pemilik" name="pemilik" value="{{ old('pemilik', $stand->pemilik) }}" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>
                    <div>
                        <label for="no_wa" class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp Pemilik *</label>
                        <input type="text" id="no_wa" name="no_wa" value="{{ old('no_wa', $stand->no_wa) }}" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="is_active" class="block text-xs font-bold text-slate-700 mb-1">Status Operasional Stand *</label>
                        <select id="is_active" name="is_active" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                            <option value="1" {{ old('is_active', $stand->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif (Buka & Menerima Pesanan)</option>
                            <option value="0" {{ old('is_active', $stand->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Nonaktif (Tutup Sementara)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat / Menu Andalan</label>
                    <textarea id="deskripsi" name="deskripsi" rows="2"
                              class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">{{ old('deskripsi', $stand->deskripsi) }}</textarea>
                </div>
            </div>

            @if($vendorUser)
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-orange-600 mb-3">2. Akun Login Penjual (Read-only)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Login Vendor</label>
                        <div class="w-full px-3.5 py-2.5 text-sm bg-slate-100 border border-slate-200 rounded-xl font-medium text-slate-600">
                            {{ $vendorUser->email }}
                        </div>
                    </div>
                    <div class="flex items-end">
                        <a href="{{ route('admin.vendors.resetPassword', $vendorUser) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-700 font-bold text-xs border border-slate-200 transition-colors">
                            Reset Password Vendor
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.stands.index') }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-black text-xs sm:text-sm shadow-md shadow-orange-500/25 transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
