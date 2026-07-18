<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Kategori;
use App\Models\Promo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Cache data katalog yang dibaca di hampir setiap request (Fase 6).
 * Di-invalidasi otomatis saat admin mengubah produk/kategori/promo/bundle
 * (lihat AppServiceProvider::boot).
 */
class KatalogCache
{
    private const TTL = 600; // 10 menit

    private const KEYS = ['katalog.nav_kategori', 'katalog.promo_aktif', 'katalog.bundle_aktif'];

    public static function navKategori(): Collection
    {
        return Cache::remember('katalog.nav_kategori', self::TTL,
            fn () => Kategori::orderBy('nama')->get());
    }

    public static function promoAktif(): Collection
    {
        return Cache::remember('katalog.promo_aktif', self::TTL,
            fn () => Promo::aktif()->get());
    }

    public static function bundleAktif(): Collection
    {
        return Cache::remember('katalog.bundle_aktif', self::TTL,
            fn () => Bundle::aktif()->with('produk')->get());
    }

    public static function bersihkan(): void
    {
        foreach (self::KEYS as $key) {
            Cache::forget($key);
        }
    }
}
