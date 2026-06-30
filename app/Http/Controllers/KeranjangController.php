<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function index(): View
    {
        return view('keranjang.index', [
            'items'      => $this->cart->items(),
            'subtotal'   => $this->cart->subtotal(),
            'diskon'     => $this->cart->diskonTotal(),
            'total'      => $this->cart->total(),
            'namaBundle' => $this->cart->namaBundle(),
        ]);
    }

    public function tambah(Request $request, Produk $produk): RedirectResponse
    {
        $qty = max(1, (int) $request->input('qty', 1));

        if ($produk->stok < 1) {
            return back()->with('error', 'Maaf, stok produk ini habis.');
        }

        $this->cart->tambah($produk->id, min($qty, $produk->stok));

        if ($request->boolean('beli_langsung')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('sukses', $produk->nama.' ditambahkan ke keranjang.');
    }

    public function ubah(Request $request, Produk $produk): RedirectResponse
    {
        $qty = (int) $request->input('qty', 1);
        $this->cart->ubah($produk->id, min($qty, $produk->stok));

        return back();
    }

    public function hapus(Produk $produk): RedirectResponse
    {
        $this->cart->hapus($produk->id);

        return back()->with('sukses', 'Produk dihapus dari keranjang.');
    }
}
