<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    public function ulasan(): HasMany
    {
        return $this->hasMany(Ulasan::class);
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

    public function gambarUrl(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        // Gambar hasil upload admin disimpan di storage (path "produk/...").
        return str_starts_with($this->gambar, 'produk/')
            ? asset('storage/'.$this->gambar)
            : asset('images/produk/'.$this->gambar);
    }

    /* ---------------- Diskon per-produk (ilustratif, deterministik) -------- */

    public function persenDiskon(): int
    {
        return [0 => 0, 1 => 12, 2 => 0, 3 => 18][$this->id % 4] ?? 0;
    }

    public function hargaCoret(): ?float
    {
        $persen = $this->persenDiskon();

        return $persen > 0 ? round((float) $this->harga / (1 - $persen / 100), -2) : null;
    }

    /* ---------------- Rating & terjual (DATA ASLI dari ulasan/pesanan) ----- */

    public function ratingRata(): float
    {
        // Pakai agregat yang sudah di-eager-load bila tersedia (hindari N+1).
        $avg = $this->ulasan_avg_rating ?? $this->ulasan()->avg('rating');

        return round((float) $avg, 1);
    }

    public function jumlahUlasan(): int
    {
        return (int) ($this->ulasan_count ?? $this->ulasan()->count());
    }

    /** Teks rating untuk kartu: angka bila ada ulasan, "Baru" bila belum. */
    public function ratingTampil(): string
    {
        return $this->jumlahUlasan() > 0 ? number_format($this->ratingRata(), 1) : 'Baru';
    }

    /** Jumlah terjual nyata (dari pesanan yang sudah dibayar). */
    public function terjualTampil(): int
    {
        return (int) DB::table('detail_pesanan')
            ->join('pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->where('detail_pesanan.produk_id', $this->id)
            ->whereIn('pesanan.status', ['lunas', 'diproses', 'dikirim', 'selesai'])
            ->sum('detail_pesanan.jumlah');
    }

    /** Apakah user pernah membeli (lunas) produk ini -> boleh memberi ulasan. */
    public function sudahDibeliOleh(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return DB::table('detail_pesanan')
            ->join('pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->where('detail_pesanan.produk_id', $this->id)
            ->where('pesanan.user_id', $userId)
            ->whereIn('pesanan.status', ['lunas', 'diproses', 'dikirim', 'selesai'])
            ->exists();
    }
}
