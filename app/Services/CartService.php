<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Produk;
use App\Models\Voucher;
use Illuminate\Support\Collection;

/**
 * Keranjang belanja berbasis session (guest cart).
 * Struktur session: ['keranjang' => [produk_id => qty]].
 */
class CartService
{
    private const KEY = 'keranjang';
    private const KEY_VOUCHER = 'voucher_kode';
    private const KEY_BUNDLE  = 'bundle_diskon'; // ['nama' => , 'nilai' => int]

    public function tambah(int $produkId, int $qty = 1): void
    {
        $items = $this->raw();
        $items[$produkId] = ($items[$produkId] ?? 0) + $qty;
        $this->simpan($items);
    }

    public function ubah(int $produkId, int $qty): void
    {
        $items = $this->raw();
        if ($qty <= 0) {
            unset($items[$produkId]);
        } else {
            $items[$produkId] = $qty;
        }
        $this->simpan($items);
    }

    public function hapus(int $produkId): void
    {
        $items = $this->raw();
        unset($items[$produkId]);
        $this->simpan($items);
    }

    public function kosongkan(): void
    {
        session()->forget([self::KEY, self::KEY_VOUCHER, self::KEY_BUNDLE]);
    }

    /* ---------------- Voucher ---------------- */

    public function pasangVoucher(string $kode): void
    {
        session()->put(self::KEY_VOUCHER, strtoupper($kode));
    }

    public function lepasVoucher(): void
    {
        session()->forget(self::KEY_VOUCHER);
    }

    public function voucher(): ?Voucher
    {
        $kode = session()->get(self::KEY_VOUCHER);

        return $kode ? Voucher::where('kode', $kode)->first() : null;
    }

    public function diskonVoucher(): int
    {
        $v = $this->voucher();

        return $v ? $v->potongan($this->subtotal()) : 0;
    }

    /* ---------------- Bundle ---------------- */

    /** Tambahkan paket: masukkan produknya + catat penghematan paket. */
    public function tambahBundle(Bundle $bundle): void
    {
        foreach ($bundle->produk as $p) {
            $this->tambah($p->id, (int) $p->pivot->jumlah);
        }

        $diskon = (int) $bundle->hemat() + $this->diskonBundle();
        session()->put(self::KEY_BUNDLE, ['nama' => $bundle->nama, 'nilai' => $diskon]);
    }

    public function diskonBundle(): int
    {
        return (int) (session()->get(self::KEY_BUNDLE)['nilai'] ?? 0);
    }

    public function namaBundle(): ?string
    {
        return session()->get(self::KEY_BUNDLE)['nama'] ?? null;
    }

    public function diskonTotal(): int
    {
        return $this->diskonVoucher() + $this->diskonBundle();
    }

    public function total(): float
    {
        return max(0, $this->subtotal() - $this->diskonTotal());
    }

    /** @return Collection<int, array{produk: Produk, qty: int, subtotal: float}> */
    public function items(): Collection
    {
        $raw = $this->raw();
        if (empty($raw)) {
            return collect();
        }

        return Produk::with('kategori')
            ->whereIn('id', array_keys($raw))
            ->get()
            ->map(fn (Produk $p) => [
                'produk'   => $p,
                'qty'      => $raw[$p->id],
                'subtotal' => (float) $p->harga * $raw[$p->id],
            ])
            ->values();
    }

    public function jumlahItem(): int
    {
        return array_sum($this->raw());
    }

    public function subtotal(): float
    {
        return $this->items()->sum('subtotal');
    }

    /** Total berat keranjang dalam gram (untuk hitung ongkir). */
    public function beratGram(): int
    {
        return (int) $this->items()->sum(fn ($i) => $i['produk']->berat_gram * $i['qty']);
    }

    public function kosong(): bool
    {
        return empty($this->raw());
    }

    private function raw(): array
    {
        return session()->get(self::KEY, []);
    }

    private function simpan(array $items): void
    {
        session()->put(self::KEY, $items);
    }
}
