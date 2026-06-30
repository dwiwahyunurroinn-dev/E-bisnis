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

    /* ------------------------------------------------------------------
     | Helper tampilan untuk UI marketplace.
     | Catatan: rating, jumlah terjual, dan diskon di bawah ini bersifat
     | ILUSTRATIF (dihitung deterministik dari id) selama belum ada modul
     | ulasan & promo sungguhan (Fase 3). Mudah diganti data asli nanti.
     * ------------------------------------------------------------------ */

    public function emoji(): string
    {
        return match ($this->kategori?->slug) {
            'meja'       => '🪵',
            'rak-lemari' => '🗄️',
            'kursi'      => '🪑',
            'dekorasi'   => '🕯️',
            default      => '🛋️',
        };
    }

    public function persenDiskon(): int
    {
        return [0 => 0, 1 => 12, 2 => 0, 3 => 18][$this->id % 4] ?? 0;
    }

    public function hargaCoret(): ?float
    {
        $persen = $this->persenDiskon();

        return $persen > 0 ? round((float) $this->harga / (1 - $persen / 100), -2) : null;
    }

    public function ratingTampil(): string
    {
        return number_format(4.5 + ($this->id % 5) / 10, 1);
    }

    public function terjualTampil(): int
    {
        return 8 + ($this->id * 17) % 140;
    }
}
