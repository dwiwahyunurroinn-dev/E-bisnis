<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Services\KatalogCache;
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
            ->withAvg('ulasan', 'rating')
            ->withCount('ulasan')
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
            'kategori'      => KatalogCache::navKategori(),
            'kategoriAktif' => $kategoriAktif,
            'promos'        => KatalogCache::promoAktif(),
            'bundles'       => KatalogCache::bundleAktif(),
        ]);
    }

    /**
     * Detail satu produk.
     */
    public function show(Produk $produk): View
    {
        $produk->load(['kategori', 'bahanBaku', 'ulasan' => fn ($q) => $q->with('user')->latest()]);

        $terkait = Produk::aktif()
            ->withAvg('ulasan', 'rating')->withCount('ulasan')
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->take(4)
            ->get();

        $userId      = auth()->id();
        $bolehUlas   = $produk->sudahDibeliOleh($userId);
        $ulasanSaya  = $userId ? $produk->ulasan->firstWhere('user_id', $userId) : null;

        return view('produk.show', compact('produk', 'terkait', 'bolehUlas', 'ulasanSaya'));
    }
}
