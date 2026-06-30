<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Promo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PromoController extends Controller
{
    public function index(): View
    {
        return view('admin.promo.index', ['promos' => Promo::orderBy('urutan')->get()]);
    }

    public function create(): View
    {
        return view('admin.promo.form', ['promo' => new Promo(['warna' => '#2c8064', 'aktif' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['aktif'] = $request->boolean('aktif');
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('promo', 'public');
        }

        $promo = Promo::create($data);
        ActivityLog::catat('membuat', 'Promo #'.$promo->id, $promo->judul);

        return redirect()->route('admin.promo.index')->with('sukses', 'Promo ditambahkan.');
    }

    public function edit(Promo $promo): View
    {
        return view('admin.promo.form', compact('promo'));
    }

    public function update(Request $request, Promo $promo): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['aktif'] = $request->boolean('aktif');
        if ($request->hasFile('gambar')) {
            if ($promo->gambar) {
                Storage::disk('public')->delete($promo->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('promo', 'public');
        }

        $promo->update($data);
        ActivityLog::catat('mengubah', 'Promo #'.$promo->id, $promo->judul);

        return redirect()->route('admin.promo.index')->with('sukses', 'Promo diperbarui.');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        if ($promo->gambar) {
            Storage::disk('public')->delete($promo->gambar);
        }
        $judul = $promo->judul;
        $promo->delete();
        ActivityLog::catat('menghapus', 'Promo', $judul);

        return back()->with('sukses', 'Promo dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul'    => ['required', 'string', 'max:150'],
            'subjudul' => ['nullable', 'string', 'max:200'],
            'label'    => ['nullable', 'string', 'max:60'],
            'warna'    => ['required', 'string', 'max:30'],
            'tautan'   => ['nullable', 'string', 'max:255'],
            'urutan'   => ['required', 'integer', 'min:0'],
            'gambar'   => ['nullable', 'image', 'max:4096'],
        ]);
    }
}
