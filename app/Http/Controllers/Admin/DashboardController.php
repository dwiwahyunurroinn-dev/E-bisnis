<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hariIni = today();

        $penjualanHariIni = Pesanan::whereDate('created_at', $hariIni)
            ->whereIn('status', ['lunas', 'diproses', 'dikirim', 'selesai'])
            ->sum('total');

        $penjualanBulanIni = Pesanan::whereMonth('created_at', $hariIni->month)
            ->whereYear('created_at', $hariIni->year)
            ->whereIn('status', ['lunas', 'diproses', 'dikirim', 'selesai'])
            ->sum('total');

        // Grafik 7 hari terakhir
        $grafik = collect(range(6, 0))->map(function ($i) {
            $tgl = today()->subDays($i);

            return [
                'label' => $tgl->isoFormat('dd, D MMM'),
                'total' => (int) Pesanan::whereDate('created_at', $tgl)
                    ->whereIn('status', ['lunas', 'diproses', 'dikirim', 'selesai'])
                    ->sum('total'),
            ];
        });

        return view('admin.dashboard', [
            'penjualanHariIni'  => $penjualanHariIni,
            'penjualanBulanIni' => $penjualanBulanIni,
            'pesananPending'    => Pesanan::where('status', 'pending')->count(),
            'totalPesanan'      => Pesanan::count(),
            'totalProduk'       => Produk::count(),
            'totalPelanggan'    => User::where('role', 'pelanggan')->count(),
            'stokRendah'        => Produk::where('stok', '<', 5)->orderBy('stok')->take(5)->get(),
            'pesananTerbaru'    => Pesanan::with('user')->latest()->take(6)->get(),
            'grafik'            => $grafik,
        ]);
    }
}
