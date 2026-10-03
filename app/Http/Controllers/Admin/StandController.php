<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
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
    public function index(Request $request)
    {
        $periode = $request->query('periode', 'hari_ini');

        // Base query for time-sensitive order metrics
        $orderQuery = Order::query();

        match ($periode) {
            'hari_ini' => $orderQuery->whereDate('created_at', today()),
            'minggu_ini' => $orderQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'bulan_ini' => $orderQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'semua' => null,
            default => $orderQuery->whereDate('created_at', today()),
        };

        if (! in_array($periode, ['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'], true)) {
            $periode = 'hari_ini';
        }

        $periodeLabel = match ($periode) {
            'hari_ini' => 'Hari Ini',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'semua' => 'Keseluruhan',
        };

        // Time-sensitive metrics (updated based on period filter)
        $totalOrders = (clone $orderQuery)->count();
        $totalRevenue = (clone $orderQuery)->where('status', 'selesai')->sum('total_harga');
        $completedOrdersCount = (clone $orderQuery)->where('status', 'selesai')->count();

        // Build a period-scoped date constraint for reuse
        $periodScope = match ($periode) {
            'hari_ini' => fn ($q) => $q->whereDate('created_at', today()),
            'minggu_ini' => fn ($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'bulan_ini' => fn ($q) => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'semua' => fn ($q) => $q,
            default => fn ($q) => $q->whereDate('created_at', today()),
        };

        // Static / Master Data (constant cumulative)
        // orders_count dipersempit ke periode aktif agar badge di tabel stand konsisten
        $stands = Stand::with('user')
            ->withCount([
                'menus',
                'orders as orders_count' => $periodScope,
            ])
            ->get();
        $totalStands = $stands->count();
        $activeStands = $stands->where('is_active', true)->count();
        $totalUsers = User::count();
        $totalMenus = Menu::count();
        $availableMenus = Menu::where('is_available', true)->where('stok', '>', 0)->count();

        // Recent orders: maksimum 50, difilter sesuai periode
        $recentOrders = Order::with(['stand', 'items.menu'])
            ->tap($periodScope)
            ->latest()
            ->take(50)
            ->get();

        return view('admin.dashboard', compact(
            'stands',
            'totalStands',
            'activeStands',
            'totalUsers',
            'totalMenus',
            'availableMenus',
            'totalOrders',
            'totalRevenue',
            'completedOrdersCount',
            'recentOrders',
            'periode',
            'periodeLabel'
        ));
    }

    /**
     * Display standalone stands management page.
     */
    public function manageIndex(Request $request)
    {
        $periode = $request->query('periode', 'hari_ini');
        if (! in_array($periode, ['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'], true)) {
            $periode = 'hari_ini';
        }

        $periodeLabel = match ($periode) {
            'hari_ini' => 'Hari Ini',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'semua' => 'Keseluruhan',
        };

        $orderQuery = Order::query();
        match ($periode) {
            'hari_ini' => $orderQuery->whereDate('created_at', today()),
            'minggu_ini' => $orderQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'bulan_ini' => $orderQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'semua' => null,
            default => $orderQuery->whereDate('created_at', today()),
        };

        $totalOrdersInPeriod = $orderQuery->count();

        $query = Stand::with('user')->withCount(['orders', 'menus']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_stand', 'like', "%{$search}%")
                    ->orWhere('pemilik', 'like', "%{$search}%")
                    ->orWhere('nomor_stand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->status === '1';
            $query->where('is_active', $isActive);
        }

        $stands = $query->latest()->get();
        $totalStands = Stand::count();
        $activeStands = Stand::where('is_active', true)->count();
        $inactiveStands = Stand::where('is_active', false)->count();

        return view('admin.stands.index', compact(
            'stands',
            'totalStands',
            'activeStands',
            'inactiveStands',
            'totalOrdersInPeriod',
            'periode',
            'periodeLabel'
        ));
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
     * Show form to edit an existing stand.
     */
    public function edit(Stand $stand)
    {
        $vendorUser = $stand->user;

        return view('admin.stands.edit', compact('stand', 'vendorUser'));
    }

    /**
     * Update the stand data.
     */
    public function update(Request $request, Stand $stand)
    {
        $validated = $request->validate([
            'nama_stand' => ['required', 'string', 'max:100'],
            'pemilik' => ['required', 'string', 'max:100'],
            'no_wa' => ['required', 'string', 'max:20'],
            'nomor_stand' => ['required', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'in:0,1'],
        ], [
            'nama_stand.required' => 'Nama stand wajib diisi.',
            'pemilik.required' => 'Nama pemilik wajib diisi.',
            'no_wa.required' => 'No. WA wajib diisi.',
            'nomor_stand.required' => 'Nomor stand wajib diisi.',
        ]);

        if ($request->has('is_active')) {
            $validated['is_active'] = (bool) $request->is_active;
        }

        $stand->update($validated);

        return redirect()->route('admin.stands.index')
            ->with('success', "Stand \"{$stand->nama_stand}\" berhasil diperbarui.");
    }

    /**
     * Delete a stand and its associated vendor user.
     */
    public function destroy(Stand $stand)
    {
        $nama = $stand->nama_stand;

        DB::transaction(function () use ($stand) {
            // Delete the linked vendor user first
            $stand->user?->delete();
            $stand->delete();
        });

        return back()->with('success', "Stand \"{$nama}\" berhasil dihapus.");
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
