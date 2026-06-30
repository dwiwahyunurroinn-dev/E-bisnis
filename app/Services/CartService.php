<?php

namespace App\Services;

use App\Models\Produk;
use Illuminate\Support\Collection;

/**
 * Keranjang belanja berbasis session (guest cart).
 * Struktur session: ['keranjang' => [produk_id => qty]].
 */
class CartService
{
    private const KEY = 'keranjang';

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
        session()->forget(self::KEY);
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
