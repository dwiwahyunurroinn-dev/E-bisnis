<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class PesananService
{
    /**
     * Tandai pesanan lunas (idempoten) dan kurangi stok.
     *
     * Pengurangan stok:
     * - MySQL/MariaDB -> ditangani trigger DB (trg_kurangi_stok_produk).
     * - Driver lain (SQLite dev) -> dilakukan di aplikasi agar konsisten.
     */
    public function tandaiLunas(Pesanan $pesanan, string $metode = 'simulasi'): void
    {
        if ($pesanan->status !== 'pending') {
            return;
        }

        DB::transaction(function () use ($pesanan, $metode) {
            $pesanan->update(['status' => 'lunas', 'metode_bayar' => $metode]);

            if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
                foreach ($pesanan->detail()->get() as $d) {
                    Produk::whereKey($d->produk_id)->decrement('stok', $d->jumlah);
                }
            }
        });
    }
}
