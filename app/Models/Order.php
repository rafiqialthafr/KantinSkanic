<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_order_id',
        'kode_tr',
        'ticket_code',
        'batch_kode',
        'user_id',
        'stand_id',
        'nama_pemesan',
        'kelas',
        'total_harga',
        'metode_pembayaran',
        'status_pembayaran',
        'settlement_status',
        'settlement_id',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'integer',
        ];
    }

    public function getTicketCodeAttribute(): string
    {
        return $this->attributes['ticket_code'] ?? $this->kode_tr;
    }

    public function getTotalAmountPerStandAttribute(): int
    {
        return (int) $this->total_harga;
    }

    public function getOrderStatusAttribute(): string
    {
        return $this->status;
    }

    public function isQris(): bool
    {
        return $this->metode_pembayaran === 'qris';
    }

    public function isTunai(): bool
    {
        return $this->metode_pembayaran === 'tunai' || $this->metode_pembayaran === 'cod';
    }

    public function isLunas(): bool
    {
        return $this->status_pembayaran === 'lunas';
    }

    public function isBelumLunas(): bool
    {
        return $this->status_pembayaran === 'belum_lunas';
    }

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(ParentOrder::class, 'parent_order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(Settlement::class);
    }
}
