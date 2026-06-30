<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BahanBaku;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StokController extends Controller
{
    public function index(Request $request): View
    {
        $produk = Produk::with('kategori')
            ->when($request->q, fn ($q) => $q->where('nama', 'like', '%'.$request->q.'%'))
            ->orderBy('stok')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stok.index', [
            'produk'    => $produk,
            'bahanBaku' => BahanBaku::orderBy('nama')->get(),
        ]);
    }

    public function updateProduk(Request $request, Produk $produk): RedirectResponse
    {
        $data = $request->validate(['stok' => ['required', 'integer', 'min:0']]);
        $lama = $produk->stok;
        $produk->update($data);
        ActivityLog::catat('mengubah stok', 'Produk #'.$produk->id, $lama.' → '.$data['stok']);

        return back()->with('sukses', 'Stok '.$produk->nama.' diperbarui.');
    }

    public function updateBahan(Request $request, BahanBaku $bahan): RedirectResponse
    {
        $data = $request->validate(['stok' => ['required', 'numeric', 'min:0']]);
        $bahan->update($data);
        ActivityLog::catat('mengubah stok', 'Bahan #'.$bahan->id, $bahan->nama.' → '.$data['stok']);

        return back()->with('sukses', 'Stok bahan baku diperbarui.');
    }
}
