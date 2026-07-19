<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengaturanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'a@x.com', 'password' => 'password', 'role' => 'admin']);
    }

    public function test_halaman_pengaturan_hanya_untuk_admin(): void
    {
        $pelanggan = User::create(['name' => 'Budi', 'email' => 'b@x.com', 'password' => 'password']);

        $this->actingAs($pelanggan)->get(route('admin.pengaturan.index'))->assertForbidden();
        $this->actingAs($this->admin())->get(route('admin.pengaturan.index'))->assertOk()->assertSee('Pengaturan');
    }

    public function test_identitas_toko_tersimpan_dan_tampil_di_storefront(): void
    {
        $this->actingAs($this->admin())->post(route('admin.pengaturan.update'), [
            'nama' => 'Kayu Kita', 'instagram' => '@kayukita.id', 'whatsapp' => '081234567890',
            'email' => 'halo@kayukita.id',
        ])->assertRedirect();

        $this->assertSame('kayukita.id', Pengaturan::ambil('instagram'));   // @ dibuang
        $this->assertSame('6281234567890', Pengaturan::ambil('whatsapp'));  // 0 -> 62

        $this->get(route('produk.index'))
            ->assertSee('@kayukita.id')
            ->assertSee('wa.me/6281234567890', false)
            ->assertSee('Kayu Kita');
    }

    public function test_upload_logo_dan_qris_tersimpan(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post(route('admin.pengaturan.update'), [
            'nama' => 'Eco Craft',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
            'qris_gambar' => UploadedFile::fake()->image('qris.png', 400, 400),
        ])->assertRedirect();

        Storage::disk('public')->assertExists(Pengaturan::ambil('logo'));
        Storage::disk('public')->assertExists(Pengaturan::ambil('qris_gambar'));
    }

    public function test_rekening_tampil_di_halaman_pembayaran(): void
    {
        Pengaturan::simpan('rekening', "BCA 1234567890 a.n. Eco Craft\nBRI 555 a.n. Eco Craft");

        $user = User::create(['name' => 'Budi', 'email' => 'c@x.com', 'password' => 'password']);
        $k = Kategori::firstOrCreate(['slug' => 'meja'], ['nama' => 'Meja']);
        $p = Produk::create(['kategori_id' => $k->id, 'nama' => 'Meja', 'slug' => 'meja-uji',
            'harga' => 500000, 'berat_gram' => 5000, 'stok' => 5, 'gambar' => 'x.jpg']);

        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);
        $this->actingAs($user)->post(route('checkout.store'), [
            'nama' => 'Budi', 'telepon' => '08', 'kota' => 'Solo',
            'alamat_lengkap' => 'Jl. A', 'pengiriman' => 'jne|REG', 'pembayaran' => 'qris',
        ]);

        $this->actingAs($user)->get(route('pesanan.show', \App\Models\Pesanan::first()->kode))
            ->assertSee('Transfer Bank Manual')
            ->assertSee('BCA 1234567890');
    }
}
