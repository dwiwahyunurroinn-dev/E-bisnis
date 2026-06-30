<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function produk(int $stok = 10): Produk
    {
        $kategori = Kategori::firstOrCreate(['slug' => 'meja'], ['nama' => 'Meja']);

        return Produk::create([
            'kategori_id' => $kategori->id,
            'nama'        => 'Meja Uji',
            'slug'        => 'meja-uji',
            'harga'       => 100000,
            'berat_gram'  => 5000,
            'stok'        => $stok,
            'gambar'      => 'x.jpg',
        ]);
    }

    private function pelanggan(): User
    {
        return User::create(['name' => 'Budi', 'email' => 'budi@contoh.com', 'password' => 'password']);
    }

    public function test_bisa_menambah_produk_ke_keranjang(): void
    {
        $p = $this->produk();

        $this->post(route('keranjang.tambah', $p), ['qty' => 2])->assertRedirect();
        $this->get(route('keranjang.index'))->assertStatus(200)->assertSee('Meja Uji');
    }

    public function test_checkout_wajib_login(): void
    {
        $p = $this->produk();
        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);

        $this->get(route('checkout.index'))->assertRedirect(route('login'));
    }

    public function test_checkout_kosong_dialihkan_ke_keranjang(): void
    {
        $this->actingAs($this->pelanggan())
            ->get(route('checkout.index'))
            ->assertRedirect(route('keranjang.index'));
    }

    public function test_alur_checkout_membuat_pesanan(): void
    {
        $p = $this->produk(10);
        $this->post(route('keranjang.tambah', $p), ['qty' => 2]);

        $this->actingAs($this->pelanggan())->post(route('checkout.store'), [
            'nama'           => 'Budi',
            'telepon'        => '08123',
            'kota'           => 'Jakarta',
            'alamat_lengkap' => 'Jl. Mawar',
            'pengiriman'     => 'jne|REG',
        ])->assertRedirect();

        $pesanan = Pesanan::first();
        $this->assertNotNull($pesanan);
        $this->assertSame('pending', $pesanan->status);
        $this->assertEquals(200000, $pesanan->subtotal);
        $this->assertGreaterThan(0, $pesanan->ongkir);
        $this->assertCount(1, $pesanan->detail);
    }

    public function test_checkout_membuat_notifikasi_admin_dan_pelanggan(): void
    {
        User::create(['name' => 'Admin', 'email' => 'admin@x.com', 'password' => 'password', 'role' => 'admin']);
        $p = $this->produk(10);
        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);

        $this->actingAs($this->pelanggan())->post(route('checkout.store'), [
            'nama'           => 'Budi',
            'telepon'        => '08123',
            'kota'           => 'Jakarta',
            'alamat_lengkap' => 'Jl. Mawar',
            'pengiriman'     => 'jne|REG',
        ]);

        $this->assertDatabaseHas('notifikasis', ['tipe' => 'pesanan']);   // ke admin
        $this->assertDatabaseHas('notifikasis', ['tipe' => 'status']);    // ke pelanggan
    }

    public function test_pembayaran_mengurangi_stok(): void
    {
        $user = $this->pelanggan();
        $p = $this->produk(10);
        $this->post(route('keranjang.tambah', $p), ['qty' => 3]);
        $this->actingAs($user)->post(route('checkout.store'), [
            'nama'           => 'Ani',
            'telepon'        => '08123',
            'kota'           => 'Bandung',
            'alamat_lengkap' => 'Jl. Melati',
            'pengiriman'     => 'tiki|ECO',
        ]);

        $pesanan = Pesanan::first();
        $this->actingAs($user)->post(route('pesanan.bayar', $pesanan->kode))->assertRedirect();

        $this->assertSame('lunas', $pesanan->fresh()->status);
        $this->assertSame(7, $p->fresh()->stok);
    }

    public function test_pelanggan_bisa_membatalkan_pesanan_pending(): void
    {
        $user = $this->pelanggan();
        $p = $this->produk(10);
        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);
        $this->actingAs($user)->post(route('checkout.store'), [
            'nama' => 'Budi', 'telepon' => '08', 'kota' => 'Jakarta',
            'alamat_lengkap' => 'Jl', 'pengiriman' => 'jne|REG',
        ]);

        $pesanan = Pesanan::first();
        $this->actingAs($user)->post(route('pesanan.batal', $pesanan->kode))->assertRedirect();
        $this->assertSame('batal', $pesanan->fresh()->status);
    }

    public function test_pesanan_orang_lain_tidak_bisa_diakses(): void
    {
        $user = $this->pelanggan();
        $p = $this->produk(10);
        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);
        $this->actingAs($user)->post(route('checkout.store'), [
            'nama' => 'Budi', 'telepon' => '08', 'kota' => 'Jakarta',
            'alamat_lengkap' => 'Jl', 'pengiriman' => 'jne|REG',
        ]);
        $pesanan = Pesanan::first();

        $lain = User::create(['name' => 'Lain', 'email' => 'lain@x.com', 'password' => 'password']);
        $this->actingAs($lain)->get(route('pesanan.show', $pesanan->kode))->assertForbidden();
    }
}
