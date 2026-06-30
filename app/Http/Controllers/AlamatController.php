<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlamatController extends Controller
{
    public function index(): View
    {
        return view('akun.alamat', [
            'alamat' => Alamat::where('user_id', auth()->id())->orderByDesc('utama')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['user_id'] = auth()->id();

        $alamat = Alamat::create($data);

        // Alamat pertama otomatis jadi utama.
        if (Alamat::where('user_id', auth()->id())->count() === 1) {
            $alamat->update(['utama' => true]);
        }

        return back()->with('sukses', 'Alamat ditambahkan.');
    }

    public function update(Request $request, Alamat $alamat): RedirectResponse
    {
        $this->pastikanMilik($alamat);
        $alamat->update($this->validasi($request));

        return back()->with('sukses', 'Alamat diperbarui.');
    }

    public function destroy(Alamat $alamat): RedirectResponse
    {
        $this->pastikanMilik($alamat);
        $alamat->delete();

        return back()->with('sukses', 'Alamat dihapus.');
    }

    public function jadikanUtama(Alamat $alamat): RedirectResponse
    {
        $this->pastikanMilik($alamat);
        $alamat->jadikanUtama();

        return back()->with('sukses', 'Alamat utama diperbarui.');
    }

    private function pastikanMilik(Alamat $alamat): void
    {
        abort_unless($alamat->user_id === auth()->id(), 403);
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'label'          => ['nullable', 'string', 'max:40'],
            'penerima'       => ['required', 'string', 'max:120'],
            'telepon'        => ['required', 'string', 'max:25'],
            'kota'           => ['required', 'string', 'max:80'],
            'alamat_lengkap' => ['required', 'string'],
            'kode_pos'       => ['nullable', 'string', 'max:10'],
        ]);
    }
}
