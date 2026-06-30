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
        $pesanan = $this->milikSaya($kode, ['detail', 'alamat']);

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
        $pesanan = $this->milikSaya($kode);

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

    /** Pelanggan membatalkan pesanan yang masih menunggu pembayaran. */
    public function batal(string $kode): RedirectResponse
    {
        $pesanan = $this->milikSaya($kode);

        if ($pesanan->status !== 'pending') {
            return back()->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        $this->pesananService->batalkan($pesanan);

        return back()->with('sukses', 'Pesanan dibatalkan.');
    }

    /** Pelanggan konfirmasi barang diterima (dikirim -> selesai). */
    public function terima(string $kode): RedirectResponse
    {
        $pesanan = $this->milikSaya($kode);

        if ($pesanan->status !== 'dikirim') {
            return back()->with('error', 'Pesanan belum dikirim.');
        }

        $pesanan->update(['status' => 'selesai']);

        return back()->with('sukses', 'Terima kasih! Pesanan ditandai selesai.');
    }

    /** Ambil pesanan milik user login (atau admin), atau 403/404. */
    private function milikSaya(string $kode, array $relasi = []): Pesanan
    {
        $pesanan = Pesanan::with($relasi)->where('kode', $kode)->firstOrFail();

        abort_unless(
            $pesanan->user_id === auth()->id() || auth()->user()?->isAdmin(),
            403,
        );

        return $pesanan;
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
