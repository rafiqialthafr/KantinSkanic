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
