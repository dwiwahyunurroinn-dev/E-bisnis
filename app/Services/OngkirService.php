<?php

namespace App\Services;

/**
 * Kalkulator ongkir.
 *
 * Saat ini memakai simulasi berbasis berat (per kg) agar checkout berfungsi
 * end-to-end tanpa API key. Strukturnya sengaja dibuat mirip respons RajaOngkir
 * sehingga mudah diganti panggilan HTTP asli nanti (Fase 2d).
 */
class OngkirService
{
    /** Tarif per kg tiap layanan (simulasi). */
    private const TARIF = [
        ['kurir' => 'jne',  'nama' => 'JNE',  'layanan' => 'REG', 'per_kg' => 10000, 'etd' => '2-3 hari'],
        ['kurir' => 'jne',  'nama' => 'JNE',  'layanan' => 'YES', 'per_kg' => 22000, 'etd' => '1 hari'],
        ['kurir' => 'pos',  'nama' => 'POS',  'layanan' => 'Reguler', 'per_kg' => 8500, 'etd' => '3-4 hari'],
        ['kurir' => 'tiki', 'nama' => 'TIKI', 'layanan' => 'ECO', 'per_kg' => 7500, 'etd' => '4-5 hari'],
    ];

    /**
     * Daftar opsi ongkir untuk tujuan & berat tertentu.
     *
     * @return array<int, array{kurir:string, nama:string, layanan:string, etd:string, ongkir:int}>
     */
    public function opsi(?string $kotaTujuan, int $beratGram): array
    {
        $kg = max(1, (int) ceil($beratGram / 1000));

        // Zona sederhana: kota di luar Jawa sedikit lebih mahal (faktor).
        $faktor = $this->faktorZona($kotaTujuan);

        return array_map(function ($t) use ($kg, $faktor) {
            return [
                'kurir'   => $t['kurir'],
                'nama'    => $t['nama'],
                'layanan' => $t['layanan'],
                'etd'     => $t['etd'],
                'ongkir'  => (int) round($t['per_kg'] * $kg * $faktor, -2),
            ];
        }, self::TARIF);
    }

    /** Cari satu opsi spesifik (validasi saat order dibuat). */
    public function cari(?string $kota, int $beratGram, string $kurir, string $layanan): ?array
    {
        foreach ($this->opsi($kota, $beratGram) as $o) {
            if ($o['kurir'] === $kurir && $o['layanan'] === $layanan) {
                return $o;
            }
        }

        return null;
    }

    private function faktorZona(?string $kota): float
    {
        $kota = strtolower(trim((string) $kota));
        $jawa = ['jakarta', 'bandung', 'semarang', 'surabaya', 'yogyakarta', 'bogor', 'depok', 'bekasi', 'tangerang', 'malang', 'solo'];

        foreach ($jawa as $kotaJawa) {
            if (str_contains($kota, $kotaJawa)) {
                return 1.0;
            }
        }

        return $kota === '' ? 1.0 : 1.6; // luar Jawa / tidak dikenal
    }
}
