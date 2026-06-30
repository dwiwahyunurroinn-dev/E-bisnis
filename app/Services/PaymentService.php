<?php

namespace App\Services;

use App\Models\Pesanan;
use Illuminate\Support\Facades\Http;

/**
 * Integrasi pembayaran Midtrans Snap.
 *
 * Aktif otomatis bila MIDTRANS_SERVER_KEY diisi di .env. Bila kosong,
 * aktif() = false dan aplikasi memakai alur simulasi (tombol bayar manual).
 */
class PaymentService
{
    public function aktif(): bool
    {
        return ! empty(config('services.midtrans.server_key'));
    }

    private function baseUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Buat transaksi Snap, kembalikan redirect_url untuk halaman pembayaran.
     */
    public function buatSnap(Pesanan $pesanan): ?string
    {
        if (! $this->aktif()) {
            return null;
        }

        $pesanan->loadMissing('detail', 'alamat', 'user');

        $items = $pesanan->detail->map(fn ($d) => [
            'id'       => (string) $d->produk_id,
            'price'    => (int) $d->harga,
            'quantity' => (int) $d->jumlah,
            'name'     => mb_substr($d->nama_produk, 0, 50),
        ])->toArray();

        $items[] = [
            'id'       => 'ongkir',
            'price'    => (int) $pesanan->ongkir,
            'quantity' => 1,
            'name'     => 'Ongkos Kirim',
        ];

        $payload = [
            'transaction_details' => [
                'order_id'     => $pesanan->kode,
                'gross_amount' => (int) $pesanan->total,
            ],
            'item_details'     => $items,
            'customer_details' => [
                'first_name' => $pesanan->alamat->penerima,
                'email'      => $pesanan->user->email,
                'phone'      => $pesanan->alamat->telepon,
            ],
            'callbacks' => [
                'finish' => route('pesanan.show', $pesanan->kode),
            ],
        ];

        $response = Http::withBasicAuth(config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->post($this->baseUrl(), $payload);

        return $response->successful() ? $response->json('redirect_url') : null;
    }

    /**
     * Verifikasi signature webhook Midtrans.
     */
    public function verifikasiSignature(array $payload): bool
    {
        $expected = hash('sha512',
            ($payload['order_id'] ?? '').
            ($payload['status_code'] ?? '').
            ($payload['gross_amount'] ?? '').
            config('services.midtrans.server_key')
        );

        return hash_equals($expected, $payload['signature_key'] ?? '');
    }

    /**
     * Apakah status notifikasi Midtrans berarti LUNAS.
     */
    public function lunas(array $payload): bool
    {
        $status = $payload['transaction_status'] ?? '';
        $fraud  = $payload['fraud_status'] ?? 'accept';

        return in_array($status, ['capture', 'settlement'], true) && $fraud === 'accept';
    }
}
