<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Bundle;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function index(): View
    {
        return view('admin.bundle.index', ['bundles' => Bundle::with('produk')->latest()->get()]);
    }

    public function create(): View
    {
        return view('admin.bundle.form', [
            'bundle'    => new Bundle(['aktif' => true]),
            'semuaProduk' => Produk::orderBy('nama')->get(),
            'terpilih'  => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $bundle = Bundle::create([
            'nama'         => $data['nama'],
            'slug'         => $this->slugUnik($data['nama']),
            'deskripsi'    => $data['deskripsi'] ?? null,
            'harga_bundle' => $data['harga_bundle'],
            'aktif'        => $request->boolean('aktif'),
        ]);
        $bundle->produk()->sync($this->pivot($data['produk']));
        ActivityLog::catat('membuat', 'Bundle #'.$bundle->id, $bundle->nama);

        return redirect()->route('admin.bundle.index')->with('sukses', 'Bundle dibuat.');
    }

    public function edit(Bundle $bundle): View
    {
        return view('admin.bundle.form', [
            'bundle'      => $bundle,
            'semuaProduk' => Produk::orderBy('nama')->get(),
            'terpilih'    => $bundle->produk->pluck('pivot.jumlah', 'id')->toArray(),
        ]);
    }

    public function update(Request $request, Bundle $bundle): RedirectResponse
    {
        $data = $this->validasi($request);
        $bundle->update([
            'nama'         => $data['nama'],
            'deskripsi'    => $data['deskripsi'] ?? null,
            'harga_bundle' => $data['harga_bundle'],
            'aktif'        => $request->boolean('aktif'),
        ]);
        $bundle->produk()->sync($this->pivot($data['produk']));
        ActivityLog::catat('mengubah', 'Bundle #'.$bundle->id, $bundle->nama);

        return redirect()->route('admin.bundle.index')->with('sukses', 'Bundle diperbarui.');
    }

    public function destroy(Bundle $bundle): RedirectResponse
    {
        $nama = $bundle->nama;
        $bundle->delete();
        ActivityLog::catat('menghapus', 'Bundle', $nama);

        return back()->with('sukses', 'Bundle dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama'         => ['required', 'string', 'max:150'],
            'deskripsi'    => ['nullable', 'string'],
            'harga_bundle' => ['required', 'numeric', 'min:0'],
            'produk'       => ['required', 'array', 'min:2'],
            'produk.*'     => ['integer', 'exists:produk,id'],
        ]);
    }

    /** @return array<int, array{jumlah:int}> */
    private function pivot(array $produkIds): array
    {
        return collect($produkIds)->mapWithKeys(fn ($id) => [$id => ['jumlah' => 1]])->toArray();
    }

    private function slugUnik(string $nama): string
    {
        $base = Str::slug($nama);
        $slug = $base;
        $i = 1;
        while (Bundle::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
