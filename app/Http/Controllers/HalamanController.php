<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/** Halaman statis: cara belanja, kebijakan retur, privasi, syarat & ketentuan. */
class HalamanController extends Controller
{
    private const HALAMAN = [
        'cara-belanja'      => 'Cara Belanja',
        'kebijakan-retur'   => 'Kebijakan Retur',
        'kebijakan-privasi' => 'Kebijakan Privasi',
        'syarat-ketentuan'  => 'Syarat & Ketentuan',
    ];

    public function tampil(string $slug): View
    {
        abort_unless(isset(self::HALAMAN[$slug]), 404);

        return view('halaman.tampil', [
            'slug'  => $slug,
            'judul' => self::HALAMAN[$slug],
        ]);
    }
}
