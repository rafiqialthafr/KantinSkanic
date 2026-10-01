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
        'kode_tr',
        'stand_id',
        'nama_pemesan',
        'kelas',
        'total_harga',
        'jam_pengambilan',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'integer',
        ];
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
