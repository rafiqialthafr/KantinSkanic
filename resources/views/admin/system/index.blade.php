@extends('layouts.admin')

@section('title', 'Status & Info Sistem - Super Admin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Status & Info Sistem')
@section('page-subtitle', \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd') . ', ' . \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'))

@section('sidebar-nav')
    @include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-4 sm:p-6 space-y-6 max-w-5xl">

    {{-- OPERASIONAL JAM PO --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-7">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Jadwal Operasional Pre-Order Kantin</h2>
                <p class="text-xs text-slate-500 mt-0.5">Siswa dapat memesan dan mengambil pesanan pada slot istirahat berikut</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sistem PO Aktif</span>
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-5 rounded-2xl bg-orange-50/60 border border-orange-200/80">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-xl bg-orange-500 text-white font-black text-xs flex items-center justify-center">1</div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Istirahat Pertama</h3>
                        <p class="text-xs text-orange-700 font-semibold">Pagi / Sesi Snack & Sarapan</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-700 font-semibold border-t border-orange-200/60 pt-3">
                    <span>Rentang Pengambilan:</span>
                    <span class="font-extrabold text-orange-600 bg-white px-2.5 py-1 rounded-lg border border-orange-200">09:45 - 10:15 WIB</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-amber-50/60 border border-amber-200/80">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white font-black text-xs flex items-center justify-center">2</div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Istirahat Kedua</h3>
                        <p class="text-xs text-amber-700 font-semibold">Siang / Sesi Makan Siang</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-700 font-semibold border-t border-amber-200/60 pt-3">
                    <span>Rentang Pengambilan:</span>
                    <span class="font-extrabold text-amber-600 bg-white px-2.5 py-1 rounded-lg border border-amber-200">12:00 - 12:45 WIB</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SYSTEM & SERVER STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6">
            <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4">Statistik Pengguna Terdaftar</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-600">Super Administrator</span>
                    <span class="font-extrabold text-slate-900 px-2 py-0.5 rounded-md bg-slate-100">{{ $userCounts['admin'] }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-600">Akun Penjual / Vendor</span>
                    <span class="font-extrabold text-orange-600 px-2 py-0.5 rounded-md bg-orange-50">{{ $userCounts['penjual'] }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-600">Akun Siswa</span>
                    <span class="font-extrabold text-blue-600 px-2 py-0.5 rounded-md bg-blue-50">{{ $userCounts['siswa'] }}</span>
                </div>
                <div class="flex items-center justify-between text-xs border-t border-slate-100 pt-3">
                    <span class="font-black text-slate-800">Total Seluruh User</span>
                    <span class="font-black text-slate-900 text-sm">{{ $userCounts['total'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6">
            <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4">Informasi Lingkungan Sistem</h2>
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-600">Aplikasi</span>
                    <span class="font-extrabold text-slate-900">KantinSkanic Pre-Order</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-600">Versi PHP</span>
                    <span class="font-extrabold text-slate-900 font-mono">{{ $systemStats['php_version'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-600">Versi Laravel</span>
                    <span class="font-extrabold text-slate-900 font-mono">v{{ $systemStats['laravel_version'] }}</span>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                    <span class="font-semibold text-slate-600">Tautan Daftar Menu Publik</span>
                    <a href="{{ route('katalog.index') }}" target="_blank" class="font-bold text-orange-600 hover:underline flex items-center gap-1">
                        <span>Buka Website</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
