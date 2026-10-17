<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentOrder extends Model
{
    use HasFactory;

    protected $table = 'parent_orders';

    protected $fillable = [
        'order_id',
        'user_id',
        'nama_pemesan',
        'kelas',
        'gross_amount',
        'payment_method',
        'payment_status',
        'snap_token',
        'snap_redirect_url',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'metadata' => 'array',
        ];
    }

    public static function generateOrderId(): string
    {
        $date = date('Ymd');
        do {
            $random = strtoupper(bin2hex(random_bytes(3))); // 6 hex chars
            $candidate = "ORDER-{$date}-{$random}";
        } while (static::where('order_id', $candidate)->exists());

        return $candidate;
    }

    public function isQris(): bool
    {
        return $this->payment_method === 'qris';
    }

    public function isCod(): bool
    {
        return $this->payment_method === 'cod' || $this->payment_method === 'tunai';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->payment_status === 'expired';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }
}
