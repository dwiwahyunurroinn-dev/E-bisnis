<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\Voucher;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DiskonController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function pasangVoucher(Request $request): RedirectResponse
    {
        $request->validate(['kode' => ['required', 'string']]);

        $voucher = Voucher::where('kode', strtoupper($request->kode))->first();

        if (! $voucher) {
            return back()->with('error', 'Kode voucher tidak ditemukan.');
        }

        if ($alasan = $voucher->alasanTidakBerlaku($this->cart->subtotal())) {
            return back()->with('error', $alasan);
        }

        $this->cart->pasangVoucher($voucher->kode);

        return back()->with('sukses', 'Voucher '.$voucher->kode.' diterapkan!');
    }

    public function lepasVoucher(): RedirectResponse
    {
        $this->cart->lepasVoucher();

        return back()->with('sukses', 'Voucher dilepas.');
    }

    public function tambahBundle(Bundle $bundle): RedirectResponse
    {
        abort_unless($bundle->aktif, 404);
        $bundle->load('produk');

        $this->cart->tambahBundle($bundle);

        return redirect()->route('keranjang.index')
            ->with('sukses', 'Paket "'.$bundle->nama.'" ditambahkan ke keranjang.');
    }
}
