<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(Request $request): View
    {
        $pelanggan = User::where('role', 'pelanggan')
            ->withCount('pesanan')
            ->withSum(['pesanan as total_belanja' => function ($q) {
                $q->whereIn('status', ['lunas', 'diproses', 'dikirim', 'selesai']);
            }], 'total')
            ->when($request->q, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$request->q.'%')
                ->orWhere('email', 'like', '%'.$request->q.'%')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function show(User $pelanggan): View
    {
        abort_unless($pelanggan->role === 'pelanggan', 404);
        $pelanggan->load(['pesanan' => fn ($q) => $q->latest()]);

        return view('admin.pelanggan.show', compact('pelanggan'));
    }
}
