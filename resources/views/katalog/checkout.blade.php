@extends('layouts.app')

@section('title', 'Mengalihkan ke Keranjang...')

@section('content')
<div class="max-w-md mx-auto px-4 py-20 text-center">
    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 mb-4 animate-spin">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
    <h3 class="text-base font-bold text-slate-800">Membuka Keranjang Pesanan...</h3>
    <p class="text-xs text-slate-500 mt-1">Anda dialihkan ke keranjang pesanan di halaman utama.</p>
</div>

<script>
    window.location.replace("{{ route('katalog.index', ['open_cart' => 1]) }}");
</script>
@endsection