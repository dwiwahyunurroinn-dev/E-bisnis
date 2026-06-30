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
