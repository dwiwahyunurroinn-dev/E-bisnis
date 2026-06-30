<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function store(Request $request, Produk $produk): RedirectResponse
    {
        if (! $produk->sudahDibeliOleh($request->user()->id)) {
            return back()->with('error', 'Anda hanya bisa mengulas produk yang sudah dibeli.');
        }

        $data = $request->validate([
            'rating'   => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string', 'max:1000'],
        ]);

        Ulasan::updateOrCreate(
            ['produk_id' => $produk->id, 'user_id' => $request->user()->id],
            ['rating' => $data['rating'], 'komentar' => $data['komentar'] ?? null],
        );

        return back()->with('sukses', 'Terima kasih atas ulasan Anda!');
    }
}
