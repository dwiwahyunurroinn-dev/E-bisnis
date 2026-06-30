<?php

namespace Tests\Feature;

use App\Models\Alamat;
use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlamatExpireTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::create(['name' => 'Budi', 'email' => 'b@b.com', 'password' => 'password']);
    }

    private function produk(): Produk
    {
        $k = Kategori::firstOrCreate(['slug' => 'meja'], ['nama' => 'Meja']);

        return Produk::create(['kategori_id' => $k->id, 'nama' => 'Meja', 'slug' => 'meja-uji',
            'harga' => 100000, 'berat_gram' => 5000, 'stok' => 10, 'gambar' => 'x.jpg']);
    }

    public function test_pelanggan_menambah_alamat_pertama_jadi_utama(): void
    {
        $user = $this->user();

        $this->actingAs($user)->post(route('akun.alamat.store'), [
            'label' => 'Rumah', 'penerima' => 'Budi', 'telepon' => '08',
            'kota' => 'Solo', 'alamat_lengkap' => 'Jl. A',
        ])->assertRedirect();

        $this->assertDatabaseHas('alamat', ['user_id' => $user->id, 'kota' => 'Solo', 'utama' => true]);
    }

    public function test_checkout_dengan_alamat_tersimpan_menyalin_snapshot(): void
    {
        $user = $this->user();
        $alamat = Alamat::create(['user_id' => $user->id, 'penerima' => 'Budi', 'telepon' => '08',
            'kota' => 'Bandung', 'alamat_lengkap' => 'Jl. Melati', 'utama' => true]);
        $p = $this->produk();
        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'alamat_id' => $alamat->id, 'pengiriman' => 'jne|REG',
        ])->assertRedirect();

        $pesanan = Pesanan::first();
        $this->assertSame('Bandung', $pesanan->kota);
        $this->assertSame('Jl. Melati', $pesanan->alamat_lengkap);
        $this->assertSame($alamat->id, $pesanan->alamat_id);
    }

    public function test_command_expire_membatalkan_pending_lama(): void
    {
        $user = $this->user();
        $lama = Pesanan::create(['kode' => 'INV-LAMA', 'user_id' => $user->id, 'penerima' => 'B',
            'telepon' => '08', 'kota' => 'X', 'alamat_lengkap' => 'Y',
            'subtotal' => 100000, 'ongkir' => 0, 'total' => 100000, 'status' => 'pending']);
        $lama->forceFill(['created_at' => now()->subHours(48)])->save();

        $baru = Pesanan::create(['kode' => 'INV-BARU', 'user_id' => $user->id, 'penerima' => 'B',
            'telepon' => '08', 'kota' => 'X', 'alamat_lengkap' => 'Y',
            'subtotal' => 100000, 'ongkir' => 0, 'total' => 100000, 'status' => 'pending']);

        $this->artisan('pesanan:expire')->assertSuccessful();

        $this->assertSame('batal', $lama->fresh()->status);
        $this->assertSame('pending', $baru->fresh()->status);
    }
}
