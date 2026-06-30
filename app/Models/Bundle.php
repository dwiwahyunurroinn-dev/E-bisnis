<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bundle extends Model
{
    protected $fillable = ['nama', 'slug', 'deskripsi', 'harga_bundle', 'aktif'];

    protected $casts = [
        'harga_bundle' => 'decimal:2',
        'aktif'        => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function produk(): BelongsToMany
    {
        return $this->belongsToMany(Produk::class, 'bundle_produk')->withPivot('jumlah');
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true);
    }

    /** Total harga normal seluruh item paket. */
    public function hargaNormal(): float
    {
        return $this->produk->sum(fn ($p) => (float) $p->harga * $p->pivot->jumlah);
    }

    /** Nilai penghematan paket. */
    public function hemat(): float
    {
        return max(0, $this->hargaNormal() - (float) $this->harga_bundle);
    }
}
