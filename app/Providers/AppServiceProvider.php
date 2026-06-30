<?php

namespace App\Providers;

use App\Models\Kategori;
use App\Services\CartService;
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
        // Data global navbar: daftar kategori + jumlah item keranjang.
        View::composer('layouts.app', function ($view) {
            $view->with('navKategori', Kategori::orderBy('nama')->get());
            $view->with('cartCount', app(CartService::class)->jumlahItem());
        });
    }
}
