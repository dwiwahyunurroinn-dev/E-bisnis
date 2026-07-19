<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembayaranTest extends TestCase
{
    use RefreshDatabase;

    private function produk(int $stok = 10): Produk
    {
        $k = Kategori::firstOrCreate(['slug' => 'meja'], ['nama' => 'Meja']);

        return Produk::create(['kategori_id' => $k->id, 'nama' => 'Meja', 'slug' => 'meja-'.uniqid(),
            'harga' => 500000, 'berat_gram' => 5000, 'stok' => $stok, 'gambar' => 'x.jpg']);
    }

    private function checkout(User $user, Produk $p, string $metode): void
    {
        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);
        $this->actingAs($user)->post(route('checkout.store'), [
            'nama' => 'Budi', 'telepon' => '08', 'kota' => 'Solo',
            'alamat_lengkap' => 'Jl. A', 'pengiriman' => 'jne|REG', 'pembayaran' => $metode,
        ]);
    }

    public function test_halaman_checkout_menampilkan_semua_metode(): void
    {
        $user = User::create(['name' => 'Budi', 'email' => 'm@b.com', 'password' => 'password']);
        $p = $this->produk();
        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);

        $this->actingAs($user)->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Metode Pembayaran')
            ->assertSee('QRIS')
            ->assertSee('BCA Virtual Account')
            ->assertSee('Mandiri Virtual Account')
            ->assertSee('GoPay')
            ->assertSee('ShopeePay')
            ->assertSee('Bayar di Tempat (COD)');
    }

    public function test_checkout_va_menyimpan_metode_dan_menampilkan_nomor_va(): void
    {
        $user = User::create(['name' => 'Budi', 'email' => 'b@b.com', 'password' => 'password']);
        $this->checkout($user, $this->produk(), 'va_bca');

        $pesanan = Pesanan::first();
        $this->assertSame('va_bca', $pesanan->metode_bayar);
        $this->assertStringStartsWith('3901', $pesanan->nomorVa());

        $this->actingAs($user)->get(route('pesanan.show', $pesanan->kode))
            ->assertSee('BCA Virtual Account')
            ->assertSee($pesanan->nomorVa());
    }

    public function test_checkout_qris_menampilkan_panel_qr(): void
    {
        $user = User::create(['name' => 'Budi', 'email' => 'q@b.com', 'password' => 'password']);
        $this->checkout($user, $this->produk(), 'qris');

        $pesanan = Pesanan::first();
        $this->actingAs($user)->get(route('pesanan.show', $pesanan->kode))
            ->assertSee('QRIS')
            ->assertSee('Scan kode QR');
    }

    public function test_checkout_cod_langsung_dikonfirmasi_dan_stok_berkurang(): void
    {
        $user = User::create(['name' => 'Budi', 'email' => 'c@b.com', 'password' => 'password']);
        $p = $this->produk(10);
        $this->checkout($user, $p, 'cod');

        $pesanan = Pesanan::first();
        $this->assertSame('lunas', $pesanan->status);
        $this->assertSame('cod', $pesanan->metode_bayar);
        $this->assertSame(9, $p->fresh()->stok);

        $this->actingAs($user)->get(route('pesanan.show', $pesanan->kode))
            ->assertSee('Pesanan COD Dikonfirmasi');
    }

    public function test_bayar_simulasi_mempertahankan_metode_pilihan(): void
    {
        $user = User::create(['name' => 'Budi', 'email' => 'g@b.com', 'password' => 'password']);
        $this->checkout($user, $this->produk(), 'gopay');

        $pesanan = Pesanan::first();
        $this->actingAs($user)->post(route('pesanan.bayar', $pesanan->kode))->assertRedirect();

        $pesanan->refresh();
        $this->assertSame('lunas', $pesanan->status);
        $this->assertSame('gopay', $pesanan->metode_bayar);
    }

    public function test_metode_tidak_dikenal_ditolak(): void
    {
        $user = User::create(['name' => 'Budi', 'email' => 'x@b.com', 'password' => 'password']);
        $p = $this->produk();
        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'nama' => 'Budi', 'telepon' => '08', 'kota' => 'Solo',
            'alamat_lengkap' => 'Jl. A', 'pengiriman' => 'jne|REG', 'pembayaran' => 'bitcoin',
        ])->assertSessionHasErrors('pembayaran');
    }
}
