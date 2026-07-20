<?php

namespace Tests\Feature;

use App\Models\Bundle;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PraDeployTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'a@x.com', 'password' => 'password', 'role' => 'admin']);
    }

    private function kategori(): Kategori
    {
        return Kategori::firstOrCreate(['slug' => 'meja'], ['nama' => 'Meja']);
    }

    /* ---------------- Upload foto produk ---------------- */

    private function dataProduk(array $extra = []): array
    {
        return array_merge([
            'kategori_id' => $this->kategori()->id, 'nama' => 'Meja Uji', 'harga' => 100000,
            'berat_gram' => 5000, 'stok' => 5, 'status' => 'aktif',
        ], $extra);
    }

    public function test_admin_upload_foto_produk_tersimpan_dan_tampil(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post(route('admin.produk.store'),
            $this->dataProduk(['gambar' => UploadedFile::fake()->image('meja.jpg', 800, 800)])
        )->assertRedirect();

        $produk = Produk::where('nama', 'Meja Uji')->first();
        $this->assertNotNull($produk);
        $this->assertStringStartsWith('produk/', $produk->gambar);
        Storage::disk('public')->assertExists($produk->gambar);
        $this->assertStringContainsString('storage/'.$produk->gambar, $produk->gambarUrl());
    }

    public function test_ganti_foto_menghapus_file_lama(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.produk.store'),
            $this->dataProduk(['gambar' => UploadedFile::fake()->image('lama.jpg')]));
        $produk = Produk::where('nama', 'Meja Uji')->first();
        $fileLama = $produk->gambar;

        $this->actingAs($admin)->put(route('admin.produk.update', $produk),
            $this->dataProduk(['gambar' => UploadedFile::fake()->image('baru.jpg')]));

        $produk->refresh();
        Storage::disk('public')->assertMissing($fileLama);
        Storage::disk('public')->assertExists($produk->gambar);
    }

    public function test_hapus_produk_ikut_menghapus_fotonya(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.produk.store'),
            $this->dataProduk(['gambar' => UploadedFile::fake()->image('meja.jpg')]));
        $produk = Produk::where('nama', 'Meja Uji')->first();
        $file = $produk->gambar;

        $this->actingAs($admin)->delete(route('admin.produk.destroy', $produk));

        Storage::disk('public')->assertMissing($file);
    }

    public function test_file_bukan_gambar_dan_terlalu_besar_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        // File PHP menyamar sebagai upload -> harus ditolak (validasi image).
        $this->actingAs($admin)->post(route('admin.produk.store'),
            $this->dataProduk(['gambar' => UploadedFile::fake()->create('jahat.php', 10, 'text/x-php')])
        )->assertSessionHasErrors('gambar');

        // Gambar melebihi 4 MB -> ditolak.
        $this->actingAs($admin)->post(route('admin.produk.store'),
            $this->dataProduk(['gambar' => UploadedFile::fake()->create('besar.jpg', 5000, 'image/jpeg')])
        )->assertSessionHasErrors('gambar');

        $this->assertDatabaseMissing('produk', ['nama' => 'Meja Uji']);
    }

    /* ---------------- Crop banner promo ---------------- */

    public function test_hasil_crop_banner_promo_tersimpan(): void
    {
        Storage::fake('public');

        // JPEG kecil valid sebagai data URL (meniru hasil kanvas crop di browser).
        $im = imagecreatetruecolor(30, 10);
        ob_start();
        imagejpeg($im);
        $jpeg = base64_encode((string) ob_get_clean());

        $this->actingAs($this->admin())->post(route('admin.promo.store'), [
            'judul' => 'Promo Crop', 'warna' => '#2c8064', 'urutan' => 1, 'aktif' => 1,
            'gambar_crop' => 'data:image/jpeg;base64,'.$jpeg,
        ])->assertRedirect();

        $promo = \App\Models\Promo::where('judul', 'Promo Crop')->first();
        $this->assertNotNull($promo->gambar);
        Storage::disk('public')->assertExists($promo->gambar);
    }

    public function test_data_crop_palsu_diabaikan(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post(route('admin.promo.store'), [
            'judul' => 'Promo Palsu', 'warna' => '#2c8064', 'urutan' => 1,
            'gambar_crop' => 'data:image/jpeg;base64,BUKANGAMBAR===',
        ])->assertRedirect();

        // Promo tetap dibuat, tapi tanpa gambar (data tak valid diabaikan).
        $this->assertNull(\App\Models\Promo::where('judul', 'Promo Palsu')->first()->gambar);
    }

    /* ---------------- Keamanan: diskon bundle tak bisa dimanipulasi ---------------- */

    public function test_diskon_bundle_hangus_bila_item_paket_dihapus(): void
    {
        $k = $this->kategori();
        $meja = Produk::create(['kategori_id' => $k->id, 'nama' => 'Meja', 'slug' => 'meja-b',
            'harga' => 1000000, 'berat_gram' => 5000, 'stok' => 5, 'gambar' => 'x.jpg']);
        $rak = Produk::create(['kategori_id' => $k->id, 'nama' => 'Rak', 'slug' => 'rak-b',
            'harga' => 500000, 'berat_gram' => 5000, 'stok' => 5, 'gambar' => 'x.jpg']);
        $bundle = Bundle::create(['nama' => 'Paket', 'slug' => 'paket', 'harga_bundle' => 1200000, 'aktif' => true]);
        $bundle->produk()->attach([$meja->id => ['jumlah' => 1], $rak->id => ['jumlah' => 1]]);

        $this->post(route('bundle.tambah', $bundle));
        $this->assertSame(300000, app(CartService::class)->diskonBundle()); // 1.5jt - 1.2jt

        // Manipulasi: hapus item mahal dari keranjang -> diskon harus hangus.
        $this->delete(route('keranjang.hapus', $meja));
        $this->assertSame(0, app(CartService::class)->diskonBundle());
    }

    /* ---------------- Header keamanan ---------------- */

    public function test_header_keamanan_terpasang(): void
    {
        $this->get(route('produk.index'))
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
