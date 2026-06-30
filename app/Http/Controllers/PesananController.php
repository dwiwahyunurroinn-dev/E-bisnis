<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function show(string $kode): View
    {
        $pesanan = Pesanan::with('detail', 'alamat')->where('kode', $kode)->firstOrFail();

        return view('pesanan.show', compact('pesanan'));
    }

    /**
     * Simulasi pembayaran (pengganti webhook Midtrans untuk sekarang).
     * Mengubah status menjadi 'lunas' -> memicu trigger MySQL pengurang stok.
     */
    public function bayar(string $kode): RedirectResponse
    {
        $pesanan = Pesanan::where('kode', $kode)->firstOrFail();

        if ($pesanan->status === 'pending') {
            $pesanan->update([
                'status'       => 'lunas',
                'metode_bayar' => 'simulasi',
            ]);

            // Pengurangan stok:
            // - MySQL/MariaDB  -> ditangani trigger DB (trg_kurangi_stok_produk).
            // - Driver lain (mis. SQLite dev) -> dilakukan di aplikasi agar konsisten.
            if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
                foreach ($pesanan->detail()->get() as $d) {
                    Produk::whereKey($d->produk_id)->decrement('stok', $d->jumlah);
                }
            }
        }

        return redirect()->route('pesanan.show', $pesanan->kode)
            ->with('sukses', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
    }
}
