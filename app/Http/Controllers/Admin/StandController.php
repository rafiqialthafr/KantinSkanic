<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StandController extends Controller
{
    /**
     * Admin dashboard overview.
     */
    public function index()
    {
        $stands = Stand::with('user')->withCount(['orders', 'menus'])->get();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'selesai')->sum('total_harga');
        $recentOrders = Order::with(['stand', 'items.menu'])->latest()->take(20)->get();

        return view('admin.dashboard', compact('stands', 'totalOrders', 'totalRevenue', 'recentOrders'));
    }

    /**
     * Show form to create a new stand + vendor account.
     */
    public function create()
    {
        return view('admin.stands.create');
    }

    /**
     * Store a new stand and its vendor user account atomically.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_stand' => ['required', 'string', 'max:100'],
            'pemilik' => ['required', 'string', 'max:100'],
            'no_wa' => ['required', 'string', 'max:20'],
            'nomor_stand' => ['required', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'nama_stand.required' => 'Nama stand wajib diisi.',
            'pemilik.required' => 'Nama pemilik wajib diisi.',
            'no_wa.required' => 'No. WA wajib diisi.',
            'nomor_stand.required' => 'Nomor stand wajib diisi.',
            'email.required' => 'Email akun vendor wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password akun vendor wajib diisi.',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Create the stand first
            $stand = Stand::create([
                'nama_stand' => $validated['nama_stand'],
                'pemilik' => $validated['pemilik'],
                'no_wa' => $validated['no_wa'],
                'nomor_stand' => $validated['nomor_stand'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'is_active' => true,
            ]);

            // 2. Create the vendor user linked to this stand
            User::create([
                'name' => $validated['pemilik'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'penjual',
                'stand_id' => $stand->id,
            ]);
        });

        return redirect()->route('admin.dashboard')
            ->with('success', "Stand \"{$validated['nama_stand']}\" beserta akun vendor berhasil ditambahkan.");
    }

    /**
     * Show reset password form for a vendor.
     */
    public function showResetPassword(User $user)
    {
        abort_unless($user->isPenjual(), 404);

        return view('admin.stands.reset_password', compact('user'));
    }

    /**
     * Reset a vendor's password.
     */
    public function resetPassword(Request $request, User $user)
    {
        abort_unless($user->isPenjual(), 404);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('admin.dashboard')
            ->with('success', "Password akun {$user->name} berhasil direset.");
    }

    /**
     * Toggle a stand's active status.
     */
    public function toggleActive(Stand $stand)
    {
        $stand->update(['is_active' => ! $stand->is_active]);

        $status = $stand->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Stand \"{$stand->nama_stand}\" berhasil {$status}.");
    }
}
