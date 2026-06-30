<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class KatalogSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = collect([
            ['nama' => 'Meja',         'slug' => 'meja'],
            ['nama' => 'Rak & Lemari', 'slug' => 'rak-lemari'],
            ['nama' => 'Kursi',        'slug' => 'kursi'],
            ['nama' => 'Dekorasi',     'slug' => 'dekorasi'],
        ])->mapWithKeys(fn ($k) => [$k['slug'] => Kategori::create($k)]);

        $bahan = collect([
            ['nama' => 'Kayu jati bekas peti kemas',       'satuan' => 'kg',     'stok' => 500],
            ['nama' => 'Kayu pinus palet bekas',           'satuan' => 'kg',     'stok' => 800],
            ['nama' => 'Besi hollow daur ulang',           'satuan' => 'batang', 'stok' => 120],
            ['nama' => 'Cat water-based ramah lingkungan', 'satuan' => 'liter',  'stok' => 60],
        ])->map(fn ($b) => BahanBaku::create($b));

        $produk = [
            [
                'kategori' => 'meja',
                'nama' => 'Meja Belajar Minimalis Jati Reclaimed',
                'slug' => 'meja-belajar-jati-reclaimed',
                'deskripsi' => 'Meja belajar dari kayu jati bekas peti kemas, finishing natural water-based.',
                'dimensi' => '120 x 60 x 75 cm', 'harga' => 1250000, 'berat_gram' => 18000, 'stok' => 12,
                'gambar' => 'meja-belajar.jpg',
                'bahan' => [[0, 12], [3, 0.5]],
            ],
            [
                'kategori' => 'rak-lemari',
                'nama' => 'Rak Buku 4 Tingkat Kayu Palet',
                'slug' => 'rak-buku-4-tingkat-palet',
                'deskripsi' => 'Rak buku kokoh dari kayu pinus palet daur ulang, cocok untuk ruang kerja.',
                'dimensi' => '80 x 30 x 150 cm', 'harga' => 890000, 'berat_gram' => 15000, 'stok' => 8,
                'gambar' => 'rak-buku.jpg',
                'bahan' => [[1, 10], [3, 0.4]],
            ],
            [
                'kategori' => 'kursi',
                'nama' => 'Kursi Cafe Industrial Reclaimed',
                'slug' => 'kursi-cafe-industrial',
                'deskripsi' => 'Kursi dengan dudukan kayu reclaimed dan kaki besi hollow daur ulang.',
                'dimensi' => '45 x 45 x 90 cm', 'harga' => 450000, 'berat_gram' => 6000, 'stok' => 25,
                'gambar' => 'kursi-cafe.jpg',
                'bahan' => [[1, 3], [2, 1], [3, 0.2]],
            ],
            [
                'kategori' => 'dekorasi',
                'nama' => 'Lampu Hias Gantung Kayu',
                'slug' => 'lampu-hias-gantung-kayu',
                'deskripsi' => 'Dekorasi lampu gantung dari potongan kayu sisa produksi.',
                'dimensi' => '25 x 25 x 30 cm', 'harga' => 275000, 'berat_gram' => 2000, 'stok' => 30,
                'gambar' => 'lampu-hias.jpg',
                'bahan' => [[1, 1.5]],
            ],
            [
                'kategori' => 'meja',
                'nama' => 'Meja Kopi Bulat Live Edge',
                'slug' => 'meja-kopi-bulat-live-edge',
                'deskripsi' => 'Meja kopi dengan tepi alami (live edge) dari potongan kayu utuh.',
                'dimensi' => 'Diameter 70 x 40 cm', 'harga' => 1650000, 'berat_gram' => 22000, 'stok' => 5,
                'gambar' => 'meja-kopi.jpg',
                'bahan' => [[0, 15], [3, 0.6]],
            ],
            [
                'kategori' => 'rak-lemari',
                'nama' => 'Lemari Pajang 2 Pintu',
                'slug' => 'lemari-pajang-2-pintu',
                'deskripsi' => 'Lemari pajang dua pintu dari kombinasi kayu jati dan pinus reclaimed.',
                'dimensi' => '100 x 40 x 180 cm', 'harga' => 2350000, 'berat_gram' => 35000, 'stok' => 4,
                'gambar' => 'lemari-pajang.jpg',
                'bahan' => [[0, 20], [1, 8], [3, 0.8]],
            ],
        ];

        foreach ($produk as $p) {
            $model = Produk::create([
                'kategori_id' => $kategori[$p['kategori']]->id,
                'nama' => $p['nama'],
                'slug' => $p['slug'],
                'deskripsi' => $p['deskripsi'],
                'dimensi' => $p['dimensi'],
                'harga' => $p['harga'],
                'berat_gram' => $p['berat_gram'],
                'stok' => $p['stok'],
                'gambar' => $p['gambar'],
            ]);

            foreach ($p['bahan'] as [$idx, $jumlah]) {
                $model->bahanBaku()->attach($bahan[$idx]->id, ['jumlah' => $jumlah]);
            }
        }
    }
}
