<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Pengaturan toko (key-value) yang bisa diubah admin dari panel:
 * identitas toko, logo, barcode QRIS, rekening bank, dsb.
 * Nilai identitas menimpa config('toko.*') — lihat AppServiceProvider.
 */
class Pengaturan extends Model
{
    protected $fillable = ['kunci', 'nilai'];

    /** @return array<string, string|null> */
    public static function semua(): array
    {
        return Cache::remember('pengaturan.semua', 3600,
            fn () => static::pluck('nilai', 'kunci')->toArray());
    }

    public static function ambil(string $kunci, ?string $default = null): ?string
    {
        return static::semua()[$kunci] ?? $default;
    }

    public static function simpan(string $kunci, ?string $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        Cache::forget('pengaturan.semua');
    }
}
