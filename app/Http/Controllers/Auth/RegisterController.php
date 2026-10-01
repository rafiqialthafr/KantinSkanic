<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Show student quick-register form.
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('katalog.index');
        }

        return view('auth.register');
    }

    /**
     * Register a new siswa account and redirect to checkout/intended.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'kelas' => ['required', 'string', 'max:20'],
            'no_wa' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'no_wa.required' => 'No. WA wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar, silakan login.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
        ]);

        // Store kelas in session for pre-filling checkout form
        session(['siswa_kelas' => $validated['kelas'], 'siswa_wa' => $validated['no_wa']]);

        Auth::login($user);

        return redirect()->intended(route('katalog.index'))
            ->with('success', 'Akun berhasil dibuat! Selesaikan pesananmu.');
    }
}
