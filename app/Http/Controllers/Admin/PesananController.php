<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pesanan;
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(Request $request): View
    {
        $pesanan = Pesanan::with('user')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->q, fn ($q) => $q->where('kode', 'like', '%'.$request->q.'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pesanan.index', compact('pesanan'));
    }

    public function show(Pesanan $pesanan): View
    {
        $pesanan->load('detail', 'alamat', 'user');

        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function update(Request $request, Pesanan $pesanan, PesananService $service): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,lunas,diproses,dikirim,selesai,batal'],
        ]);

        // Bila admin menandai lunas dari pending, pakai service agar stok berkurang.
        if ($data['status'] === 'lunas' && $pesanan->status === 'pending') {
            $service->tandaiLunas($pesanan, 'manual-admin');
        } else {
            $pesanan->update(['status' => $data['status']]);
        }

        ActivityLog::catat('mengubah', 'Pesanan '.$pesanan->kode, 'status → '.$data['status']);

        return back()->with('sukses', 'Status pesanan diperbarui.');
    }
}
