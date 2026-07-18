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
        return self::ingat('katalog.nav_kategori', fn () => Kategori::orderBy('nama')->get());
    }

    public static function promoAktif(): Collection
    {
        return self::ingat('katalog.promo_aktif', fn () => Promo::aktif()->get());
    }

    public static function bundleAktif(): Collection
    {
        return self::ingat('katalog.bundle_aktif', fn () => Bundle::aktif()->with('produk')->get());
    }

    /**
     * Cache dengan pengaman: bila isi cache tidak bisa di-unserialize utuh
     * (mis. kelas di luar daftar cache.serializable_classes), buang dan hitung ulang
     * supaya storefront tidak pernah tumbang karena cache korup.
     */
    private static function ingat(string $key, \Closure $resolver): Collection
    {
        $nilai = Cache::remember($key, self::TTL, $resolver);

        if (! $nilai instanceof Collection || $nilai->contains(fn ($item) => $item instanceof \__PHP_Incomplete_Class)) {
            Cache::forget($key);
            $nilai = $resolver();
        }

        return $nilai;
    }

    public static function bersihkan(): void
    {
        foreach (self::KEYS as $key) {
            Cache::forget($key);
        }
    }
}
