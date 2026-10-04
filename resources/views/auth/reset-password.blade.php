@extends('layouts.auth')

@section('title', 'Reset Kata Sandi - KantinSkanic')

@section('content')
<div class="max-w-md mx-auto px-4 py-4 sm:py-8 w-full">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 p-6 sm:p-8">

        <!-- Icon & Header -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-orange-500/20 mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900">Buat Kata Sandi Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-xs mx-auto leading-relaxed">
                Pastikan kata sandi baru kamu kuat dan mudah diingat.
            </p>
        </div>

        {{-- Validation errors --}}
        @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-0.5">
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Hidden token --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                <div class="relative">
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $email) }}"
                           required autocomplete="email"
                           placeholder="nama@kantin.com"
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium @error('email') border-rose-400 ring-1 ring-rose-300 @enderror">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
            </div>

            {{-- New Password --}}
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           placeholder="Min. 8 karakter"
                           autocomplete="new-password"
                           class="w-full pl-10 pr-10 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium @error('password') border-rose-400 ring-1 ring-rose-300 @enderror">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <button type="button" onclick="togglePassword('password', 'eye-new-pass')"
                            class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                        <svg id="eye-new-pass" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>

                {{-- Strength indicator --}}
                <div class="mt-2 space-y-1" id="strength-wrap" style="display:none;">
                    <div class="flex gap-1">
                        <div id="s1" class="h-1 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                        <div id="s2" class="h-1 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                        <div id="s3" class="h-1 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                        <div id="s4" class="h-1 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                    </div>
                    <p id="strength-label" class="text-[10px] text-slate-400 font-semibold"></p>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           placeholder="Ulangi kata sandi baru"
                           autocomplete="new-password"
                           class="w-full pl-10 pr-10 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium @error('password_confirmation') border-rose-400 ring-1 ring-rose-300 @enderror">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm-pass')"
                            class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                        <svg id="eye-confirm-pass" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 active:scale-98 text-white font-extrabold text-sm shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                <span>Simpan Kata Sandi Baru</span>
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-orange-600 font-semibold hover:underline transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Kembali ke halaman Masuk
            </a>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.innerHTML = isHidden
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
    }

    // Password strength meter
    const pwInput = document.getElementById('password');
    const strengthWrap = document.getElementById('strength-wrap');
    const strengthLabel = document.getElementById('strength-label');
    const bars = [document.getElementById('s1'), document.getElementById('s2'), document.getElementById('s3'), document.getElementById('s4')];
    const levels = [
        { color: 'bg-rose-500',   label: 'Terlalu lemah' },
        { color: 'bg-amber-500',  label: 'Lemah' },
        { color: 'bg-yellow-400', label: 'Cukup' },
        { color: 'bg-emerald-500', label: 'Kuat' },
    ];

    pwInput.addEventListener('input', function () {
        const val = this.value;
        if (!val) { strengthWrap.style.display = 'none'; return; }
        strengthWrap.style.display = 'block';

        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        bars.forEach((bar, i) => {
            bar.className = 'h-1 flex-1 rounded-full transition-all duration-300 ' +
                (i < score ? levels[score - 1].color : 'bg-slate-200');
        });
        strengthLabel.textContent = levels[score - 1]?.label ?? '';
    });
</script>
@endsection
