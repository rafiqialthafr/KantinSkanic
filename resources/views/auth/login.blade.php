@extends('layouts.auth')

@section('title', 'Masuk - KantinSkanic')

@section('content')
<div class="w-full flex-1 flex flex-col lg:flex-row min-h-screen bg-white">

    <!-- LEFT COLUMN: Form Side -->
    <div class="w-full lg:w-[38%] xl:w-[34%] flex flex-col justify-center px-6 py-10 sm:px-10 lg:px-10 xl:px-12 min-h-screen bg-white overflow-y-auto">
        <div class="max-w-md lg:max-w-[360px] mx-auto w-full my-auto">

            <!-- Brand Icon & Header -->
            <div class="mb-6">
                <img src="{{ asset('img/kanic-logo.png') }}" alt="Logo KantinSkanic" class="w-12 h-12 rounded-2xl shadow-lg shadow-orange-500/25 mb-4 object-contain">
                <h1 class="text-2xl sm:text-[28px] font-black tracking-tight text-[#0f2942] leading-tight">Masuk ke KantinSkanic</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1.5 font-medium leading-relaxed">Satu portal masuk untuk Siswa, Penjual Stand, & Admin</p>
            </div>

            <!-- Alerts: info, success, errors -->
            @if(session('info'))
            <div class="mb-5 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 flex items-start gap-2.5 text-amber-900 text-xs">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <div>
                    <p class="font-bold">Pemberitahuan</p>
                    <p class="mt-0.5">{{ session('info') }}</p>
                </div>
            </div>
            @endif

            @if(session('success'))
            <div id="login-success-alert" class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2 transition-all duration-500">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <script>
                setTimeout(function() {
                    var el = document.getElementById('login-success-alert');
                    if (el) {
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(-6px)';
                        setTimeout(function() { el.style.display = 'none'; }, 500);
                    }
                }, 3000);
            </script>
            @endif

            @if($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Alamat Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-800 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="contoh@gmail.com"
                            class="w-full pl-10 pr-4 py-2.5 sm:py-3 text-xs sm:text-sm bg-slate-50 border border-slate-200/90 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium placeholder-slate-400">
                    </div>
                </div>

                <!-- Kata Sandi -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-800">Kata Sandi</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 hover:underline transition-colors">
                            Lupa Sandi?
                        </a>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-11 py-2.5 sm:py-3 text-xs sm:text-sm bg-slate-50 border border-slate-200/90 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium placeholder-slate-400">
                        <button type="button" onclick="togglePassword('password', 'eye-login')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer p-0.5">
                            <svg id="eye-login" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="group w-full mt-2 py-3.5 px-5 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 active:scale-[0.99] text-white font-bold text-sm sm:text-base shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>Masuk Sekarang</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>

            <!-- Teks Pendaftaran Siswa -->
            <div class="mt-4 text-center">
                <p class="text-xs text-slate-500 font-medium">
                    Siswa baru belum punya akun?
                    <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:text-orange-700 hover:underline">
                        Daftar
                    </a>
                </p>
            </div>

            <!-- Akun Demo Box -->
            <div class="mt-6">
                <div class="relative my-4 text-center">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200/80"></div></div>
                    <div class="relative inline-block bg-white px-3 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        AKUN DEMO (KLIK UNTUK ISI CEPAT)
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <button type="button" onclick="fillDemo('buagus@kantin.com', 'password')"
                        class="p-2.5 rounded-2xl border border-slate-200/90 bg-white hover:bg-orange-50/50 hover:border-orange-300 text-left transition-all cursor-pointer flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-orange-100 text-slate-500 group-hover:text-orange-600 flex items-center justify-center shrink-0 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-800 block truncate group-hover:text-orange-700">Kantin Bu Agus</span>
                            <span class="text-[10px] text-slate-400 block truncate">buagus@kantin.com</span>
                        </div>
                    </button>

                    <button type="button" onclick="fillDemo('pakjaka@kantin.com', 'password')"
                        class="p-2.5 rounded-2xl border border-slate-200/90 bg-white hover:bg-orange-50/50 hover:border-orange-300 text-left transition-all cursor-pointer flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-orange-100 text-slate-500 group-hover:text-orange-600 flex items-center justify-center shrink-0 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-800 block truncate group-hover:text-orange-700">Kantin Pak Jaka</span>
                            <span class="text-[10px] text-slate-400 block truncate">pakjaka@kantin.com</span>
                        </div>
                    </button>

                    <button type="button" onclick="fillDemo('bunda@kantin.com', 'password')"
                        class="p-2.5 rounded-2xl border border-slate-200/90 bg-white hover:bg-orange-50/50 hover:border-orange-300 text-left transition-all cursor-pointer flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-orange-100 text-slate-500 group-hover:text-orange-600 flex items-center justify-center shrink-0 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-800 block truncate group-hover:text-orange-700">Kantin Bunda</span>
                            <span class="text-[10px] text-slate-400 block truncate">bunda@kantin.com</span>
                        </div>
                    </button>

                    <button type="button" onclick="fillDemo('admin@kantin.com', 'password')"
                        class="p-2.5 rounded-2xl border border-slate-200/90 bg-white hover:bg-orange-50/50 hover:border-orange-300 text-left transition-all cursor-pointer flex items-center gap-2.5 group">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-orange-100 text-slate-500 group-hover:text-orange-600 flex items-center justify-center shrink-0 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-800 block truncate group-hover:text-orange-700">Super Admin</span>
                            <span class="text-[10px] text-slate-400 block truncate">admin@kantin.com</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Kembali ke Beranda Link -->
            <div class="mt-6 text-center">
                <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-orange-600 font-semibold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </div>
    <!-- RIGHT COLUMN: Hero Illustration Side (Laptop & Desktop lg: and up) -->
    <div class="hidden lg:flex flex-1 lg:w-[62%] xl:w-[66%] relative overflow-hidden bg-[#fed7aa] select-none flex-col justify-between min-h-screen">
        <!-- Full-Bleed High-Res Background Illustration -->
        <img src="{{ asset('img/auth-bg.png') }}" alt="Ilustrasi Kantin Skanic" class="absolute inset-0 w-full h-full object-cover object-bottom pointer-events-none">

        <!-- Top Overlay Brand Title & Logo (Matching reference mockup) -->
        <div class="relative z-10 flex flex-col items-center text-center px-8 pt-10 xl:pt-16 max-w-lg mx-auto">
            <!-- Glossy App Icon -->
            <img src="{{ asset('img/kanic-logo.png') }}" alt="Logo KantinSkanic" class="w-16 h-16 xl:w-20 xl:h-20 rounded-2xl xl:rounded-3xl shadow-xl shadow-orange-500/30 mb-4 object-contain">

            <!-- KantinSkanic Brand Name -->
            <h2 class="text-3xl xl:text-[42px] font-black tracking-tight leading-tight">
                <span class="text-[#0f2942]">Kantin</span><span class="text-orange-600">Skanic</span>
            </h2>

            <!-- Portal Tagline -->
            <p class="text-xs sm:text-sm xl:text-base font-bold text-[#1e3a5f]/80 mt-2 max-w-sm leading-relaxed">
                Satu portal masuk untuk Siswa,<br>Penjual Stand, & Admin
            </p>
        </div>

        <!-- Bottom spacer so illustration is fully appreciated -->
        <div class="relative z-10 pb-8"></div>
    </div>

</div>

<script>
    function fillDemo(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.innerHTML = isHidden ?
            '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />' :
            '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
    }
</script>
@endsection