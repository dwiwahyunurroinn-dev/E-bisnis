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

        // Email pembayaran diterima (di luar transaksi; gagal kirim tak menggagalkan pelunasan).
        rescue(fn () => $pesanan->user?->notify(new \App\Notifications\PembayaranDiterima($pesanan)), null, false);
    }

    /**
     * Batalkan pesanan. Bila sebelumnya sudah dibayar, kembalikan stok
     * (trigger DB hanya MENGURANGI saat lunas, tidak mengembalikan).
     */
    public function batalkan(Pesanan $pesanan): void
    {
        if (in_array($pesanan->status, ['selesai', 'batal'], true)) {
            return;
        }

        $sudahBayar = in_array($pesanan->status, ['lunas', 'diproses', 'dikirim'], true);

        DB::transaction(function () use ($pesanan, $sudahBayar) {
            if ($sudahBayar) {
                foreach ($pesanan->detail()->get() as $d) {
                    Produk::whereKey($d->produk_id)->increment('stok', $d->jumlah);
                }
            }
            $pesanan->update(['status' => 'batal']);
        });
    }
}
