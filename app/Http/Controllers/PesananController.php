<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\PaymentService;
use App\Services\PesananService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function __construct(
        private readonly PesananService $pesananService,
        private readonly PaymentService $payment,
    ) {}

    public function show(string $kode): View
    {
        $pesanan = Pesanan::with('detail', 'alamat')->where('kode', $kode)->firstOrFail();

        return view('pesanan.show', [
            'pesanan'        => $pesanan,
            'midtransAktif'  => $this->payment->aktif(),
        ]);
    }

    /**
     * Simulasi pembayaran (dipakai bila Midtrans belum dikonfigurasi).
     * Mengubah status menjadi 'lunas' -> trigger/aplikasi mengurangi stok.
     */
    public function bayar(string $kode): RedirectResponse
    {
        $pesanan = Pesanan::where('kode', $kode)->firstOrFail();

        // Jika Midtrans aktif, arahkan ke halaman pembayaran Snap.
        if ($this->payment->aktif() && $pesanan->status === 'pending') {
            if ($url = $this->payment->buatSnap($pesanan)) {
                return redirect()->away($url);
            }
        }

        $this->pesananService->tandaiLunas($pesanan, 'simulasi');

        return redirect()->route('pesanan.show', $pesanan->kode)
            ->with('sukses', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
    }

    /**
     * Webhook notifikasi Midtrans (HTTP POST dari server Midtrans).
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! $this->payment->verifikasiSignature($payload)) {
            return response()->json(['message' => 'invalid signature'], 403);
        }

        $pesanan = Pesanan::where('kode', $payload['order_id'] ?? '')->first();

        if ($pesanan && $this->payment->lunas($payload)) {
            $this->pesananService->tandaiLunas($pesanan, 'midtrans');
        }

        return response()->json(['message' => 'ok']);
    }
}
