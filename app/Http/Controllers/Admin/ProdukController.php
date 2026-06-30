<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public function index(Request $request): View
    {
        $produk = Produk::with('kategori')
            ->when($request->q, fn ($q) => $q->where('nama', 'like', '%'.$request->q.'%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.produk.index', compact('produk'));
    }

    public function create(): View
    {
        return view('admin.produk.form', [
            'produk'   => new Produk(),
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['slug'] = $this->slugUnik($data['nama']);
        $data['gambar'] = $this->simpanGambar($request);

        $produk = Produk::create($data);
        ActivityLog::catat('membuat', 'Produk #'.$produk->id, $produk->nama);

        return redirect()->route('admin.produk.index')->with('sukses', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk): View
    {
        return view('admin.produk.form', [
            'produk'   => $produk,
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $data = $this->validasi($request, $produk);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $this->simpanGambar($request, $produk);
        }

        $produk->update($data);
        ActivityLog::catat('mengubah', 'Produk #'.$produk->id, $produk->nama);

        return redirect()->route('admin.produk.index')->with('sukses', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        $nama = $produk->nama;
        $this->hapusGambar($produk);
        $produk->delete();
        ActivityLog::catat('menghapus', 'Produk', $nama);

        return back()->with('sukses', 'Produk dihapus.');
    }

    private function validasi(Request $request, ?Produk $produk = null): array
    {
        return $request->validate([
            'kategori_id' => ['required', 'exists:kategori,id'],
            'nama'        => ['required', 'string', 'max:150'],
            'deskripsi'   => ['nullable', 'string'],
            'dimensi'     => ['nullable', 'string', 'max:100'],
            'harga'       => ['required', 'numeric', 'min:0'],
            'berat_gram'  => ['required', 'integer', 'min:1'],
            'stok'        => ['required', 'integer', 'min:0'],
            'status'      => ['required', 'in:aktif,nonaktif'],
            'gambar'      => [$produk ? 'nullable' : 'nullable', 'image', 'max:4096'],
        ]);
    }

    private function simpanGambar(Request $request, ?Produk $produk = null): ?string
    {
        if (! $request->hasFile('gambar')) {
            return $produk?->gambar;
        }
        if ($produk) {
            $this->hapusGambar($produk);
        }

        // Disimpan di storage/app/public/produk, diakses via asset('storage/...').
        return $request->file('gambar')->store('produk', 'public');
    }

    private function hapusGambar(Produk $produk): void
    {
        if ($produk->gambar && str_starts_with($produk->gambar, 'produk/')) {
            \Storage::disk('public')->delete($produk->gambar);
        }
    }

    private function slugUnik(string $nama): string
    {
        $base = Str::slug($nama);
        $slug = $base;
        $i = 1;
        while (Produk::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
