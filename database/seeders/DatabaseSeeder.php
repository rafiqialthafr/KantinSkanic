<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application'\''s database.
     *
     * Architecture: Stand is created first, then User with stand_id FK.
     * Stand.user_id is deprecated in favour of users.stand_id.
     */
    public function run(): void
    {
        // ------------------------------------------------
        // 1. Super Admin
        // ------------------------------------------------
        User::updateOrCreate(
            ['email' => 'admin@kantin.com'],
            [
                'name' => 'Admin Kantin Skanic',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'stand_id' => null,
            ]
        );

        // ------------------------------------------------
        // 2. Stands + Vendor accounts (5 defaults)
        // ------------------------------------------------
        $standsData = [
            [
                'stand' => [
                    'nama_stand' => 'Kantin Bu Agus',
                    'nomor_stand' => 'Stand 01',
                    'pemilik' => 'Ibu Agus',
                    'no_wa' => '081234567890',
                    'deskripsi' => 'Spesialis Masakan Nusantara, Nasi Goreng & Mie Ayam Legendaris',
                    'is_active' => true,
                ],
                'vendor' => [
                    'name' => 'Ibu Agus',
                    'email' => 'buagus@kantin.com',
                    'password' => Hash::make('password'),
                    'role' => 'penjual',
                ],
                'menus' => [
                    ['nama_menu' => 'Nasi Goreng Spesial Telur',  'kategori' => 'makanan', 'harga' => 15000, 'stok' => 35],
                    ['nama_menu' => 'Mie Ayam Bakso Urat',         'kategori' => 'makanan', 'harga' => 13000, 'stok' => 30],
                    ['nama_menu' => 'Tahu Bakso Crispy Gurih',     'kategori' => 'snack',   'harga' => 7000, 'stok' => 25],
                    ['nama_menu' => 'Es Teh Manis Jumbo',          'kategori' => 'minuman', 'harga' => 4000, 'stok' => 50],
                    ['nama_menu' => 'Es Jeruk Peras Segar',        'kategori' => 'minuman', 'harga' => 5000, 'stok' => 40],
                ],
            ],
            [
                'stand' => [
                    'nama_stand' => 'Kantin Pak Jaka',
                    'nomor_stand' => 'Stand 02',
                    'pemilik' => 'Pak Jaka',
                    'no_wa' => '082234567891',
                    'deskripsi' => 'Soto Ayam Lamongan Gurih, Batagor Renyah & Siomay Bandung Asli',
                    'is_active' => true,
                ],
                'vendor' => [
                    'name' => 'Pak Jaka',
                    'email' => 'pakjaka@kantin.com',
                    'password' => Hash::make('password'),
                    'role' => 'penjual',
                ],
                'menus' => [
                    ['nama_menu' => 'Soto Ayam Lamongan + Nasi',   'kategori' => 'makanan', 'harga' => 14000, 'stok' => 25],
                    ['nama_menu' => 'Batagor Kuah Kacang Spesial', 'kategori' => 'snack',   'harga' => 10000, 'stok' => 30],
                    ['nama_menu' => 'Siomay Ikan Tenggiri Asli',   'kategori' => 'snack',   'harga' => 10000, 'stok' => 25],
                    ['nama_menu' => 'Es Cincau Susu Gula Aren',    'kategori' => 'minuman', 'harga' => 6000, 'stok' => 35],
                    ['nama_menu' => 'Air Mineral Botol Dingin',    'kategori' => 'minuman', 'harga' => 3000, 'stok' => 50],
                ],
            ],
            [
                'stand' => [
                    'nama_stand' => 'Kantin Bunda',
                    'nomor_stand' => 'Stand 03',
                    'pemilik' => 'Bunda Eni',
                    'no_wa' => '083334567892',
                    'deskripsi' => 'Roti Bakar Lumer, Toast Modern, Boba Milk & Cemilan Kekinian',
                    'is_active' => true,
                ],
                'vendor' => [
                    'name' => 'Bunda Eni',
                    'email' => 'bunda@kantin.com',
                    'password' => Hash::make('password'),
                    'role' => 'penjual',
                ],
                'menus' => [
                    ['nama_menu' => 'Egg & Cheese Toast Panggang',   'kategori' => 'makanan', 'harga' => 12000, 'stok' => 20],
                    ['nama_menu' => 'Roti Bakar Coklat Keju Lumer',  'kategori' => 'snack',   'harga' => 10000, 'stok' => 25],
                    ['nama_menu' => 'Cireng Krispi Bumbu Rujak',     'kategori' => 'snack',   'harga' => 6000, 'stok' => 30],
                    ['nama_menu' => 'Brown Sugar Boba Fresh Milk',   'kategori' => 'minuman', 'harga' => 10000, 'stok' => 30],
                    ['nama_menu' => 'Thai Tea Original Ice',          'kategori' => 'minuman', 'harga' => 8000, 'stok' => 40],
                ],
            ],
            [
                'stand' => [
                    'nama_stand' => 'Kantin Pak Dedi',
                    'nomor_stand' => 'Stand 04',
                    'pemilik' => 'Pak Dedi',
                    'no_wa' => '084434567893',
                    'deskripsi' => 'Warteg Modern — Nasi Sayur Lengkap & Lauk Pilihan Harian',
                    'is_active' => true,
                ],
                'vendor' => [
                    'name' => 'Pak Dedi',
                    'email' => 'pakdedi@kantin.com',
                    'password' => Hash::make('password'),
                    'role' => 'penjual',
                ],
                'menus' => [
                    ['nama_menu' => 'Nasi + Ayam Goreng Tepung', 'kategori' => 'makanan', 'harga' => 16000, 'stok' => 30],
                    ['nama_menu' => 'Nasi + Tempe Orek Pedas',   'kategori' => 'makanan', 'harga' => 10000, 'stok' => 40],
                    ['nama_menu' => 'Sayur Lodeh Santan',        'kategori' => 'makanan', 'harga' => 8000, 'stok' => 25],
                    ['nama_menu' => 'Teh Hangat Gula Aren',      'kategori' => 'minuman', 'harga' => 4000, 'stok' => 50],
                    ['nama_menu' => 'Kerupuk Udang Crispy',      'kategori' => 'snack',   'harga' => 2000, 'stok' => 100],
                ],
            ],
            [
                'stand' => [
                    'nama_stand' => 'Kantin Kak Rini',
                    'nomor_stand' => 'Stand 05',
                    'pemilik' => 'Kak Rini',
                    'no_wa' => '085534567894',
                    'deskripsi' => 'Minuman Kekinian — Jus Buah Segar, Smoothies & Minuman Sehat',
                    'is_active' => true,
                ],
                'vendor' => [
                    'name' => 'Kak Rini',
                    'email' => 'kakrini@kantin.com',
                    'password' => Hash::make('password'),
                    'role' => 'penjual',
                ],
                'menus' => [
                    ['nama_menu' => 'Jus Alpukat Susu Kental',   'kategori' => 'minuman', 'harga' => 10000, 'stok' => 30],
                    ['nama_menu' => 'Smoothie Mangga Tropicana',  'kategori' => 'minuman', 'harga' => 12000, 'stok' => 25],
                    ['nama_menu' => 'Es Campur Spesial Boba',     'kategori' => 'minuman', 'harga' => 9000, 'stok' => 30],
                    ['nama_menu' => 'Puding Coklat Lumer',        'kategori' => 'snack',   'harga' => 6000, 'stok' => 20],
                    ['nama_menu' => 'Pisang Goreng Keju Meleleh', 'kategori' => 'snack',   'harga' => 7000, 'stok' => 25],
                ],
            ],
        ];

        foreach ($standsData as $entry) {
            // Create stand first (no user_id dependency)
            $stand = Stand::updateOrCreate(
                ['nomor_stand' => $entry['stand']['nomor_stand']],
                $entry['stand']
            );

            // Create vendor user with stand_id FK
            $vendorData = $entry['vendor'];
            $vendorData['stand_id'] = $stand->id;
            $email = $vendorData['email'];

            User::updateOrCreate(
                ['email' => $email],
                $vendorData
            );

            // Create menus
            foreach ($entry['menus'] as $menuData) {
                Menu::updateOrCreate(
                    ['stand_id' => $stand->id, 'nama_menu' => $menuData['nama_menu']],
                    array_merge($menuData, ['is_available' => true, 'foto' => null])
                );
            }
        }
    }
}
