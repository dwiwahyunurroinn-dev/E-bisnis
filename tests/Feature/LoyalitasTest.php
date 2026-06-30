<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyalitasTest extends TestCase
{
    use RefreshDatabase;

    private function produk(int $harga = 600000): Produk
    {
        $k = Kategori::firstOrCreate(['slug' => 'meja'], ['nama' => 'Meja']);

        return Produk::create([
            'kategori_id' => $k->id, 'nama' => 'Meja', 'slug' => 'meja-'.uniqid(),
            'harga' => $harga, 'berat_gram' => 5000, 'stok' => 10, 'gambar' => 'x.jpg',
        ]);
    }

    public function test_voucher_mengurangi_total_pesanan(): void
    {
        $p = $this->produk(600000);
        Voucher::create(['kode' => 'HEMAT10', 'tipe' => 'persen', 'nilai' => 10, 'min_belanja' => 0, 'aktif' => true]);

        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);
        $this->post(route('voucher.pasang'), ['kode' => 'HEMAT10'])->assertRedirect();

        $this->post(route('checkout.store'), [
            'nama' => 'A', 'email' => 'a@a.com', 'telepon' => '08', 'kota' => 'Jakarta',
            'alamat_lengkap' => 'Jl', 'pengiriman' => 'jne|REG',
        ]);

        $pesanan = Pesanan::first();
        $this->assertEquals(60000, $pesanan->diskon);       // 10% dari 600.000
        $this->assertSame('HEMAT10', $pesanan->kode_voucher);
    }

    public function test_pembeli_bisa_memberi_ulasan(): void
    {
        $user = User::create(['name' => 'Beli', 'email' => 'b@b.com', 'password' => 'password']);
        $p = $this->produk();
        $pesanan = Pesanan::create([
            'kode' => 'INV-1', 'user_id' => $user->id, 'alamat_id' => $this->alamat($user),
            'subtotal' => 600000, 'ongkir' => 0, 'total' => 600000, 'status' => 'lunas',
        ]);
        DetailPesanan::create(['pesanan_id' => $pesanan->id, 'produk_id' => $p->id, 'nama_produk' => 'Meja', 'harga' => 600000, 'jumlah' => 1, 'subtotal' => 600000]);

        $this->actingAs($user)->post(route('ulasan.store', $p), ['rating' => 5, 'komentar' => 'Bagus'])->assertRedirect();
        $this->assertDatabaseHas('ulasans', ['produk_id' => $p->id, 'user_id' => $user->id, 'rating' => 5]);
    }

    public function test_non_pembeli_tidak_bisa_ulas(): void
    {
        $user = User::create(['name' => 'X', 'email' => 'x@x.com', 'password' => 'password']);
        $p = $this->produk();

        $this->actingAs($user)->post(route('ulasan.store', $p), ['rating' => 5]);
        $this->assertDatabaseMissing('ulasans', ['produk_id' => $p->id, 'user_id' => $user->id]);
    }

    private function alamat(User $user): int
    {
        return \App\Models\Alamat::create([
            'user_id' => $user->id, 'penerima' => 'A', 'telepon' => '08', 'alamat_lengkap' => 'Jl',
        ])->id;
    }
}
