<?php

namespace App\Providers;

use App\Models\Bundle;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Promo;
use App\Services\CartService;
use App\Services\KatalogCache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cache katalog kedaluwarsa otomatis saat datanya berubah (Fase 6).
        foreach ([Kategori::class, Produk::class, Promo::class, Bundle::class] as $model) {
            $model::saved(fn () => KatalogCache::bersihkan());
            $model::deleted(fn () => KatalogCache::bersihkan());
        }

        // Data global navbar: daftar kategori (cache) + jumlah item keranjang.
        View::composer('layouts.app', function ($view) {
            $view->with('navKategori', KatalogCache::navKategori());
            $view->with('cartCount', app(CartService::class)->jumlahItem());

            if ($user = auth()->user()) {
                $view->with('notifBelum', $user->notifikasi()->belumDibaca()->count());
                $view->with('notifList', $user->notifikasi()->latest()->take(6)->get());
            }
        });

        // Lonceng notifikasi pada panel admin.
        View::composer('layouts.admin', function ($view) {
            if ($user = auth()->user()) {
                $view->with('notifBelum', $user->notifikasi()->belumDibaca()->count());
                $view->with('notifList', $user->notifikasi()->latest()->take(6)->get());
            }
        });
    }
}
