<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'kategori_id', 'nama', 'slug', 'deskripsi', 'dimensi',
        'harga', 'berat_gram', 'stok', 'gambar', 'status',
    ];

    protected $casts = [
        'harga'      => 'decimal:2',
        'stok'       => 'integer',
        'berat_gram' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function bahanBaku(): BelongsToMany
    {
        return $this->belongsToMany(BahanBaku::class, 'produk_bahan_baku')
            ->withPivot('jumlah');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function tersedia(): bool
    {
        return $this->stok > 0;
    }
}
