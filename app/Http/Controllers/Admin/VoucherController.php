<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(): View
    {
        return view('admin.voucher.index', ['vouchers' => Voucher::latest()->paginate(15)]);
    }

    public function create(): View
    {
        // Saran kode acak untuk generator.
        return view('admin.voucher.form', [
            'voucher'   => new Voucher(['tipe' => 'persen', 'aktif' => true, 'min_belanja' => 0]),
            'kodeAcak'  => 'ECO'.strtoupper(Str::random(5)),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['aktif'] = $request->boolean('aktif');
        $voucher = Voucher::create($data);
        ActivityLog::catat('membuat', 'Voucher '.$voucher->kode);

        return redirect()->route('admin.voucher.index')->with('sukses', 'Voucher dibuat.');
    }

    public function edit(Voucher $voucher): View
    {
        return view('admin.voucher.form', ['voucher' => $voucher, 'kodeAcak' => $voucher->kode]);
    }

    public function update(Request $request, Voucher $voucher): RedirectResponse
    {
        $data = $this->validasi($request, $voucher);
        $data['aktif'] = $request->boolean('aktif');
        $voucher->update($data);
        ActivityLog::catat('mengubah', 'Voucher '.$voucher->kode);

        return redirect()->route('admin.voucher.index')->with('sukses', 'Voucher diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $kode = $voucher->kode;
        $voucher->delete();
        ActivityLog::catat('menghapus', 'Voucher '.$kode);

        return back()->with('sukses', 'Voucher dihapus.');
    }

    private function validasi(Request $request, ?Voucher $voucher = null): array
    {
        $data = $request->validate([
            'kode'          => ['required', 'string', 'max:30', Rule::unique('vouchers')->ignore($voucher?->id)],
            'tipe'          => ['required', 'in:persen,nominal'],
            'nilai'         => ['required', 'numeric', 'min:1'],
            'maks_potongan' => ['nullable', 'numeric', 'min:0'],
            'min_belanja'   => ['required', 'numeric', 'min:0'],
            'kuota'         => ['nullable', 'integer', 'min:1'],
            'kadaluarsa'    => ['nullable', 'date'],
        ]);
        $data['kode'] = strtoupper($data['kode']);

        return $data;
    }
}
