<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Stand extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_stand',
        'pemilik',
        'no_wa',
        'bank_name',
        'nomor_rekening',
        'atas_nama',
        'nomor_stand',
        'deskripsi',
        'is_active',
    ];

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function saldoQrisPending(): int
    {
        return (int) $this->orders()
            ->where('status', 'selesai')
            ->where('metode_pembayaran', 'qris')
            ->where('settlement_status', 'pending')
            ->sum('total_harga');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * The primary vendor user account linked to this stand.
     * (users.stand_id -> stands.id)
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * All users assigned to this stand.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
