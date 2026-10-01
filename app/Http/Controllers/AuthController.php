<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form. Redirect if already authenticated.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        if (str_contains(session()->get('url.intended', ''), 'checkout') && ! session()->has('info')) {
            session()->flash('info', 'Silakan login terlebih dahulu untuk menyelesaikan pesanan.');
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt with role-based redirect.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Role-based redirect — respect intended() for siswa guest cart flow
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Selamat datang, Admin!');
            }

            if ($user->isPenjual()) {
                return redirect()->route('vendor.dashboard')
                    ->with('success', 'Selamat datang, '.$user->name.'!');
            }

            // Siswa — honor intended URL (e.g. /checkout after guest cart)
            return redirect()->intended(route('katalog.index'))
                ->with('success', 'Selamat datang, '.$user->name.'!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Redirect user to the correct dashboard based on their role.
     */
    protected function redirectByRole($user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'penjual' => redirect()->route('vendor.dashboard'),
            default => redirect()->route('katalog.index'),
        };
    }
}
