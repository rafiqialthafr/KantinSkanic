<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stand_id',
        'kode_settlement',
        'total_dana',
        'status',
        'catatan',
        'bukti_transfer',
        'ditransfer_at',
        'ditransfer_oleh',
    ];

    protected $casts = [
        'total_dana' => 'integer',
        'ditransfer_at' => 'datetime',
    ];

    /**
     * Get the stand that received the settlement.
     */
    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    /**
     * Get the admin user who processed the transfer.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditransfer_oleh');
    }

    /**
     * Get all orders covered under this settlement.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
