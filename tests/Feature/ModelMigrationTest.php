<?php

namespace Tests\Feature;

use App\Models\Stand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_stand_menu_order_and_items_with_relations(): void
    {
        $stand = Stand::create([
            'nama_stand' => 'Stand 1 - Bakso Mas Eko',
            'nomor_stand' => 'Kantin No. 01',
            'pemilik' => 'Mas Eko',
            'no_wa' => '08123456789',
            'deskripsi' => 'Bakso lezat higienis',
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => 'Mas Eko',
            'email' => 'eko@kantin.com',
            'password' => bcrypt('password123'),
            'role' => 'penjual',
            'stand_id' => $stand->id,
        ]);

        $this->assertEquals('penjual', $user->role);
        $this->assertEquals($stand->id, $user->stand_id);
        $this->assertEquals($stand->id, $user->stand->id);
        $this->assertTrue($stand->is_active);

        $menu = $stand->menus()->create([
            'nama_menu' => 'Bakso Urat Mantap',
            'kategori' => 'makanan',
            'harga' => 15000,
            'stok' => 50,
            'is_available' => true,
        ]);

        $this->assertEquals($stand->id, $menu->stand->id);
        $this->assertTrue($menu->is_available);

        $order = $stand->orders()->create([
            'kode_tr' => 'PO-8921',
            'nama_pemesan' => 'Budi',
            'kelas' => 'XI PPLG 1',
            'total_harga' => 15000,
            'jam_pengambilan' => 'Istirahat 1',
            'status' => 'pending',
            'catatan' => 'Pedas, kuah banyak',
        ]);

        $this->assertEquals('PO-8921', $order->kode_tr);
        $this->assertEquals('pending', $order->status);

        $item = $order->items()->create([
            'menu_id' => $menu->id,
            'jumlah' => 1,
            'harga_satuan' => 15000,
            'subtotal' => 15000,
        ]);

        $this->assertEquals($menu->id, $item->menu->id);
        $this->assertEquals(1, $order->items()->count());
    }
}
