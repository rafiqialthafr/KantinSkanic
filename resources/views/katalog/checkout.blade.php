@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan (Checkout) - KantinSkanic')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-4">
        <a href="{{ route('katalog.index') }}" class="hover:text-orange-600 transition-colors">Daftar Menu</a>
        <span>&rsaquo;</span>
        <span class="text-orange-600 font-bold">Checkout Pre-Order</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Konfirmasi & Checkout Pesanan</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Periksa kembali menu pilihanmu dan lengkapi data pemesanan.</p>
    </div>

    <!-- Empty Cart Alert (Shown if cart is empty) -->
    <div id="checkout-empty-state" class="hidden bg-white rounded-3xl border border-dashed border-slate-300 p-8 sm:p-12 text-center shadow-xs">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900">Keranjang Belanja Kamu Masih Kosong</h3>
        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">Silakan pilih makanan atau minuman favoritmu terlebih dahulu dari daftar menu kantin.</p>
        <a href="{{ route('katalog.index') }}#katalog-section" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs sm:text-sm shadow-md shadow-orange-500/20 transition-all">
            <span>&larr; Pilih Menu di Daftar Menu</span>
        </a>
    </div>

    <!-- Main Checkout Grid -->
    <div id="checkout-content" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Items in Cart -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                        <h2 class="text-sm font-extrabold text-slate-900">Daftar Menu Pesanan</h2>
                    </div>
                    <span id="checkout-item-count" class="text-xs font-semibold text-slate-500">0 menu</span>
                </div>

                <div id="checkout-items-list" class="space-y-3">
                    <!-- Populated by JavaScript -->
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Butuh ubah menu?</span>
                    <a href="{{ route('katalog.index') }}#katalog-section" class="font-bold text-orange-600 hover:underline">+ Tambah Menu Lain</a>
                </div>
            </div>

            <!-- Stand Info Callout -->
            <div class="rounded-2xl bg-amber-50/70 border border-amber-200/80 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <div class="text-xs text-amber-900 leading-relaxed">
                    <p class="font-bold">Informasi Pre-Order Multi-Stand:</p>
                    <p class="mt-0.5 text-amber-800">Jika memesan dari stand yang berbeda, sistem akan otomatis memisahkan kode pesanan sesuai stand agar penjual dapat menyiapkan pesananmu secara langsung.</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Checkout Form & Summary -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs sticky top-24">
                <h2 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4">Informasi Pemesan</h2>

                <form id="page-checkout-form" onsubmit="submitPageCheckout(event)" class="space-y-4">
                    @csrf

                    <!-- Nama Pemesan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemesan *</label>
                        <input type="text" id="chk-nama" required value="{{ $user->name }}"
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>

                    <!-- Kelas Siswa -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kelas Siswa *</label>
                        <input type="text" id="chk-kelas" required placeholder="Contoh: XI PPLG 1 / XII AKL 2" value="{{ $defaultKelas }}"
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Pesanan (Opsional)</label>
                        <textarea id="chk-catatan" rows="2" placeholder="Contoh: Sambal dipisah, es sedikit..."
                            class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium"></textarea>
                    </div>

                    <!-- Total Section -->
                    <div class="pt-3 border-t border-slate-100 space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Metode Pembayaran</span>
                            <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md">Tunai di Stand (COD)</span>
                        </div>
                        <div class="flex items-center justify-between text-base font-black text-slate-900 pt-1">
                            <span>Total Pembayaran</span>
                            <span id="chk-total-price" class="text-xl font-black text-orange-600">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" id="chk-submit-btn"
                        class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 active:scale-98 text-white font-black text-sm shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span>Konfirmasi & Kirim Pesanan</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    const CART_KEY = 'kantinskanic_cart_v1';

    function getCart() {
        try {
            return JSON.parse(localStorage.getItem(CART_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveCart(items) {
        localStorage.setItem(CART_KEY, JSON.stringify(items));
        renderCheckoutView();
        if (typeof syncNavbarCartBadge === 'function') {
            syncNavbarCartBadge();
        }
    }

    function updateItemQty(menuId, delta) {
        let items = getCart();
        let idx = items.findIndex(i => i.menu_id === menuId);
        if (idx !== -1) {
            items[idx].jumlah += delta;
            if (items[idx].jumlah <= 0) {
                items.splice(idx, 1);
            } else if (items[idx].jumlah > items[idx].maxStock) {
                items[idx].jumlah = items[idx].maxStock;
                alert('Maksimal stok tercapai: ' + items[idx].maxStock);
            }
            saveCart(items);
        }
    }

    function renderCheckoutView() {
        const items = getCart();
        const emptyState = document.getElementById('checkout-empty-state');
        const content = document.getElementById('checkout-content');
        const list = document.getElementById('checkout-items-list');
        const count = document.getElementById('checkout-item-count');
        const total = document.getElementById('chk-total-price');

        if (items.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            if (content) content.classList.add('hidden');
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');
        if (content) content.classList.remove('hidden');

        const totalItemsCount = items.reduce((sum, i) => sum + i.jumlah, 0);
        const totalPrice = items.reduce((sum, i) => sum + (i.harga * i.jumlah), 0);

        if (count) count.innerText = totalItemsCount + ' menu dipilih';
        if (total) total.innerText = 'Rp ' + totalPrice.toLocaleString('id-ID');

        if (list) {
            list.innerHTML = items.map(item => `
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <div class="flex-1 pr-3">
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs sm:text-sm font-extrabold text-slate-800 line-clamp-1">${item.nama}</h4>
                            <span class="text-[9px] font-bold bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded">${item.stand}</span>
                        </div>
                        <p class="text-xs font-black text-orange-600 mt-1">Rp ${(item.harga * item.jumlah).toLocaleString('id-ID')}</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white rounded-xl border border-slate-200 px-2 py-1 shadow-2xs">
                        <button type="button" onclick="updateItemQty(${item.menu_id}, -1)" class="w-6 h-6 flex items-center justify-center text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-lg cursor-pointer">&minus;</button>
                        <span class="text-xs font-black px-1.5">${item.jumlah}</span>
                        <button type="button" onclick="updateItemQty(${item.menu_id}, 1)" class="w-6 h-6 flex items-center justify-center text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-lg cursor-pointer">&plus;</button>
                    </div>
                </div>
            `).join('');
        }
    }

    async function submitPageCheckout(event) {
        event.preventDefault();
        const items = getCart();
        if (items.length === 0) {
            alert('Keranjang belanja kosong! Silakan pilih menu terlebih dahulu.');
            return;
        }

        const nama = document.getElementById('chk-nama').value.trim();
        const kelas = document.getElementById('chk-kelas').value.trim();
        const catatan = document.getElementById('chk-catatan').value.trim();

        const btn = document.getElementById('chk-submit-btn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Memproses Pesanan...</span>
        `;

        try {
            const payload = {
                nama_pemesan: nama,
                kelas: kelas,
                catatan: catatan,
                items: items.map(i => ({
                    menu_id: i.menu_id,
                    jumlah: i.jumlah
                }))
            };

            const response = await fetch('{{ route("checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                localStorage.removeItem(CART_KEY);
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Gagal membuat pesanan. Silakan periksa kembali data Anda.');
                btn.disabled = false;
                btn.innerHTML = `<span>Konfirmasi & Kirim Pesanan</span>`;
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat memproses pesanan.');
            btn.disabled = false;
            btn.innerHTML = `<span>Konfirmasi & Kirim Pesanan</span>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderCheckoutView();
    });
</script>
@endpush
@endsection