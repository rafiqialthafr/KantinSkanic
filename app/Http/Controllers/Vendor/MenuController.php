<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Resolve the authenticated vendor'\''s stand — abort if none.
     */
    protected function vendorStand()
    {
        $stand = Auth::user()->stand;
        abort_unless($stand, 403, 'Akun Anda belum terhubung ke Stand manapun.');

        return $stand;
    }

    /**
     * Display all menus for the vendor'\''s stand.
     */
    public function index()
    {
        $stand = $this->vendorStand();
        $menus = $stand->menus()->latest()->get();

        return view('vendor.menus.index', compact('stand', 'menus'));
    }

    /**
     * Store a new menu item scoped to vendor'\''s stand.
     */
    public function store(Request $request)
    {
        $stand = $this->vendorStand();

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
            'is_available' => $request->boolean('is_available', true),
            'foto' => $fotoPath,
        ]);

        return redirect()->route('vendor.menus.index')
            ->with('success', 'Menu baru berhasil ditambahkan!');
    }

    /**
     * Update menu — strictly scoped to vendor'\''s own stand.
     */
    public function update(Request $request, Menu $menu)
    {
        $stand = $this->vendorStand();
        abort_unless($menu->stand_id === $stand->id, 403, 'Anda tidak berhak mengedit menu ini.');

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
            'is_available' => $request->boolean('is_available'),
        ];

        if ($request->hasFile('foto')) {
            if ($menu->foto && Storage::disk('public')->exists($menu->foto)) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('vendor.menus.index')
            ->with('success', "Menu \"{$menu->nama_menu}\" berhasil diperbarui!");
    }

    /**
     * Toggle menu availability.
     */
    public function toggle(Request $request, Menu $menu)
    {
        $stand = $this->vendorStand();
        abort_unless($menu->stand_id === $stand->id, 403, 'Akses ditolak.');

        $menu->update(['is_available' => ! $menu->is_available]);

        $status = $menu->is_available ? 'Tersedia' : 'Habis';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_available' => $menu->is_available,
                'message' => "Menu {$menu->nama_menu} sekarang {$status}.",
            ]);
        }

        return back()->with('success', "Status \"{$menu->nama_menu}\" diubah menjadi {$status}.");
    }

    /**
     * Delete menu — scoped to vendor'\''s own stand.
     */
    public function destroy(Menu $menu)
    {
        $stand = $this->vendorStand();
        abort_unless($menu->stand_id === $stand->id, 403, 'Anda tidak berhak menghapus menu ini.');

        if ($menu->foto && Storage::disk('public')->exists($menu->foto)) {
            Storage::disk('public')->delete($menu->foto);
        }

        $name = $menu->nama_menu;
        $menu->delete();

        return redirect()->route('vendor.menus.index')
            ->with('success', "Menu \"{$name}\" berhasil dihapus.");
    }
}
