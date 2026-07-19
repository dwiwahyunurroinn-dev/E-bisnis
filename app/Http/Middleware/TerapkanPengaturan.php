<?php

namespace App\Http\Middleware;

use App\Models\Pengaturan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Terapkan pengaturan toko dari DB (panel admin) ke config('toko.*')
 * pada setiap request, menimpa nilai default .env.
 */
class TerapkanPengaturan
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (Schema::hasTable('pengaturans')) {
                foreach (['nama', 'tagline', 'email', 'telepon', 'whatsapp', 'whatsapp_text', 'instagram'] as $k) {
                    if ($nilai = Pengaturan::ambil($k)) {
                        config(['toko.'.$k => $nilai]);
                    }
                }
            }
        } catch (\Throwable) {
            // DB belum siap (instalasi awal) -> pakai default .env.
        }

        return $next($request);
    }
}
