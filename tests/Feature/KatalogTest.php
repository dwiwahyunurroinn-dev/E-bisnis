<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use RefreshDatabase;

    private function buatProduk(array $atribut = []): Produk
    {
        $kategori = Kategori::firstOrCreate(['slug' => 'meja'], ['nama' => 'Meja']);

        return Produk::create(array_merge([
            'kategori_id' => $kategori->id,
            'nama'        => 'Meja Belajar Jati',
            'slug'        => 'meja-belajar-jati',
            'deskripsi'   => 'Meja dari kayu jati reclaimed.',
            'dimensi'     => '120 x 60 x 75 cm',
            'harga'       => 1250000,
            'berat_gram'  => 18000,
            'stok'        => 10,
            'gambar'      => 'meja.jpg',
        ], $atribut));
    }

    public function test_galeri_menampilkan_produk_aktif(): void
    {
        $this->buatProduk();

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Meja Belajar Jati');
    }

    public function test_galeri_menyembunyikan_produk_nonaktif(): void
    {
        $this->buatProduk(['status' => 'nonaktif']);

        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('Meja Belajar Jati');
    }

    public function test_filter_kategori_bekerja(): void
    {
        $this->buatProduk();

        $this->get('/?kategori=meja')->assertStatus(200)->assertSee('Meja Belajar Jati');
        $this->get('/?kategori=kursi')->assertStatus(200)->assertDontSee('Meja Belajar Jati');
    }

    public function test_halaman_detail_produk_tampil(): void
    {
        $this->buatProduk();

        $this->get('/produk/meja-belajar-jati')
            ->assertStatus(200)
            ->assertSee('120 x 60 x 75 cm')
            ->assertSee('Tambah ke Keranjang');
    }
}
