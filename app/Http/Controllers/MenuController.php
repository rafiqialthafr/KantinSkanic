<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display all menus of seller's stand.
     */
    public function index()
    {
        $user = Auth::user();
        $stand = $user->stand;

        if (! $stand) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum memiliki Stand.');
        }

        $menus = $stand->menus()->latest()->get();

        return view('dashboard.menus', [
            'stand' => $stand,
            'menus' => $menus,
        ]);
    }

    /**
     * Store a newly created menu item.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $stand = $user->stand;

        if (! $stand) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum memiliki Stand.');
        }

        $validated = $request->validate([
            'nama_menu' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:makanan,minuman,snack'],
            'harga' => ['required', 'integer', 'min:500'],
            'stok' => ['required', 'integer', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,jfif,avif,gif', 'max:5120'],
        ], [
            'nama_menu.required' => 'Nama menu wajib diisi.',
            'kategori.required' => 'Kategori menu wajib dipilih.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.min' => 'Harga minimal Rp 500.',
            'stok.required' => 'Jumlah stok awal wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, WEBP, JFIF, AVIF, atau GIF.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('menus', 'public');
        }

        $stand->menus()->create([
            'nama_menu' => $validated['nama_menu'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'stok' => $validated['stok'],
            'is_available' => $request->has('is_available') ? true : false,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu baru berhasil ditambahkan!');
    }

    /**
     * Update the specified menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && (! $user->stand || $menu->stand_id !== $user->stand->id)) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit menu ini.');
        }

        $validated = $request->validate([
            'nama_menu' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:makanan,minuman,snack'],
            'harga' => ['required', 'integer', 'min:500'],
            'stok' => ['required', 'integer', 'min:0'],
            'is_available' => ['nullable'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,jfif,avif,gif', 'max:5120'],
        ], [
            'nama_menu.required' => 'Nama menu wajib diisi.',
            'kategori.required' => 'Kategori menu wajib dipilih.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.min' => 'Harga minimal Rp 500.',
            'stok.required' => 'Jumlah stok wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, WEBP, JFIF, AVIF, atau GIF.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $data = [
            'nama_menu' => $validated['nama_menu'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'stok' => $validated['stok'],
            'is_available' => $request->has('is_available') ? true : false,
        ];

        if ($request->hasFile('foto')) {
            // Delete old file if exists
            if ($menu->foto && Storage::disk('public')->exists($menu->foto)) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('menus.index')->with('success', "Menu '{$menu->nama_menu}' berhasil diperbarui!");
    }

    /**
     * Quick toggle availability of a menu item.
     */
    public function toggle(Request $request, Menu $menu)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && (! $user->stand || $menu->stand_id !== $user->stand->id)) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah menu ini.');
        }

        $menu->update([
            'is_available' => ! $menu->is_available,
        ]);

        $statusText = $menu->is_available ? 'Tersedia' : 'Habis';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_available' => $menu->is_available,
                'message' => "Menu {$menu->nama_menu} sekarang {$statusText}.",
            ]);
        }

        return back()->with('success', "Status menu '{$menu->nama_menu}' diubah menjadi {$statusText}.");
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && (! $user->stand || $menu->stand_id !== $user->stand->id)) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus menu ini.');
        }

        if ($menu->foto && Storage::disk('public')->exists($menu->foto)) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menuName = $menu->nama_menu;
        $menu->delete();

        return redirect()->route('menus.index')->with('success', "Menu '{$menuName}' berhasil dihapus.");
    }
}
