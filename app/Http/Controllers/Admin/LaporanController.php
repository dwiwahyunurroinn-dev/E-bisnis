<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    private const STATUS_TERBAYAR = ['lunas', 'diproses', 'dikirim', 'selesai'];

    public function index(Request $request): View
    {
        [$dari, $sampai] = $this->rentang($request);

        $pesanan = Pesanan::whereBetween('created_at', [$dari, $sampai])->get();
        $terbayar = $pesanan->whereIn('status', self::STATUS_TERBAYAR);

        // Ringkasan per hari
        $perHari = $terbayar->groupBy(fn ($p) => $p->created_at->toDateString())
            ->map(fn ($g) => ['jumlah' => $g->count(), 'total' => $g->sum('total')])
            ->sortKeys();

        return view('admin.laporan.index', [
            'dari'           => $dari->toDateString(),
            'sampai'         => $sampai->toDateString(),
            'totalPesanan'   => $pesanan->count(),
            'totalTerbayar'  => $terbayar->count(),
            'totalPendapatan'=> $terbayar->sum('total'),
            'totalOngkir'    => $terbayar->sum('ongkir'),
            'rataRata'       => $terbayar->count() ? round($terbayar->avg('total')) : 0,
            'perHari'        => $perHari,
        ]);
    }

    public function ekspor(Request $request): StreamedResponse
    {
        [$dari, $sampai] = $this->rentang($request);

        $pesanan = Pesanan::with('user')
            ->whereBetween('created_at', [$dari, $sampai])
            ->latest()
            ->get();

        $namaFile = 'laporan-'.$dari->toDateString().'-sd-'.$sampai->toDateString().'.csv';

        return response()->streamDownload(function () use ($pesanan) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Kode', 'Tanggal', 'Pelanggan', 'Status', 'Subtotal', 'Ongkir', 'Total']);
            foreach ($pesanan as $p) {
                fputcsv($out, [
                    $p->kode,
                    $p->created_at->format('Y-m-d H:i'),
                    $p->user?->name ?? '-',
                    $p->status,
                    $p->subtotal,
                    $p->ongkir,
                    $p->total,
                ]);
            }
            fclose($out);
        }, $namaFile, ['Content-Type' => 'text/csv']);
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function rentang(Request $request): array
    {
        $dari = $request->filled('dari')
            ? Carbon::parse($request->dari)->startOfDay()
            : today()->startOfMonth();

        $sampai = $request->filled('sampai')
            ? Carbon::parse($request->sampai)->endOfDay()
            : today()->endOfDay();

        return [$dari, $sampai];
    }
}
