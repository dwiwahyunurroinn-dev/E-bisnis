<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'kode', 'user_id', 'alamat_id', 'kurir', 'layanan',
        'subtotal', 'diskon', 'kode_voucher', 'ongkir', 'total', 'metode_bayar', 'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'diskon'   => 'decimal:2',
        'ongkir'   => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alamat(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'alamat_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }
}
