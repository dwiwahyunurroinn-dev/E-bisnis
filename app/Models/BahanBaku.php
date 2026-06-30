<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';

    protected $fillable = ['nama', 'satuan', 'stok'];

    protected $casts = ['stok' => 'decimal:2'];

    public function produk(): BelongsToMany
    {
        return $this->belongsToMany(Produk::class, 'produk_bahan_baku')
            ->withPivot('jumlah');
    }
}
