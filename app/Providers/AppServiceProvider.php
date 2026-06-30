<?php

namespace App\Providers;

use App\Models\Kategori;
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
        // Daftar kategori untuk navbar (dipakai di semua halaman via layout).
        View::composer('layouts.app', function ($view) {
            $view->with('navKategori', Kategori::orderBy('nama')->get());
        });
    }
}
