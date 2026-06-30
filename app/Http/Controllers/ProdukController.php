<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdukController extends Controller
{
    /**
     * Galeri / katalog produk dengan filter kategori & pencarian.
     */
    public function index(Request $request): View
    {
        $kategoriAktif = $request->query('kategori');

        $produk = Produk::query()
            ->aktif()
            ->with('kategori')
            ->when($kategoriAktif, function ($q) use ($kategoriAktif) {
                $q->whereHas('kategori', fn ($k) => $k->where('slug', $kategoriAktif));
            })
            ->when($request->query('q'), function ($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->query('q').'%');
            })
            ->tap(fn ($q) => match ($request->query('sort')) {
                'termurah' => $q->orderBy('harga'),
                'termahal' => $q->orderByDesc('harga'),
                'terlaris' => $q->orderByDesc('id'),   // proxy; ganti dgn kolom terjual nanti
                default    => $q->latest(),
            })
            ->paginate(12)
            ->withQueryString();

        return view('produk.index', [
            'produk'        => $produk,
            'kategori'      => Kategori::orderBy('nama')->get(),
            'kategoriAktif' => $kategoriAktif,
            'promos'        => Promo::aktif()->get(),
        ]);
    }

    /**
     * Detail satu produk.
     */
    public function show(Produk $produk): View
    {
        $produk->load('kategori', 'bahanBaku');

        $terkait = Produk::aktif()
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->take(4)
            ->get();

        return view('produk.show', compact('produk', 'terkait'));
    }
}
