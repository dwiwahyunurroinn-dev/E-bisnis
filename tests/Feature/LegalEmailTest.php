<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use App\Notifications\PembayaranDiterima;
use App\Notifications\PesananDibuat;
use App\Notifications\PesananDikirim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LegalEmailTest extends TestCase
{
    use RefreshDatabase;

    /* ---------------- Halaman legal ---------------- */

    public function test_semua_halaman_legal_bisa_diakses(): void
    {
        foreach ([
            'cara-belanja'      => 'Cara Belanja',
            'kebijakan-retur'   => 'Kebijakan Retur',
            'kebijakan-privasi' => 'Kebijakan Privasi',
            'syarat-ketentuan'  => 'Syarat & Ketentuan',
        ] as $slug => $judul) {
            $this->get(route('halaman', $slug))->assertOk()->assertSee($judul);
        }

        $this->get('/halaman/tidak-ada')->assertNotFound();
    }

    public function test_footer_menaut_ke_halaman_legal(): void
    {
        $this->get(route('produk.index'))
            ->assertSee(route('halaman', 'kebijakan-retur'), false)
            ->assertSee(route('halaman', 'kebijakan-privasi'), false)
            ->assertSee(route('halaman', 'syarat-ketentuan'), false);
    }

    /* ---------------- Email transaksional ---------------- */

    private function checkout(string $metode = 'va_bca'): array
    {
        $user = User::create(['name' => 'Budi', 'email' => 'b@x.com', 'password' => 'password']);
        $k = Kategori::firstOrCreate(['slug' => 'meja'], ['nama' => 'Meja']);
        $p = Produk::create(['kategori_id' => $k->id, 'nama' => 'Meja', 'slug' => 'meja-'.uniqid(),
            'harga' => 500000, 'berat_gram' => 5000, 'stok' => 5, 'gambar' => 'x.jpg']);

        $this->post(route('keranjang.tambah', $p), ['qty' => 1]);
        $this->actingAs($user)->post(route('checkout.store'), [
            'nama' => 'Budi', 'telepon' => '08', 'kota' => 'Solo',
            'alamat_lengkap' => 'Jl. A', 'pengiriman' => 'jne|REG', 'pembayaran' => $metode,
        ]);

        return [$user, Pesanan::first()];
    }

    public function test_email_pesanan_dibuat_terkirim_saat_checkout(): void
    {
        Notification::fake();
        [$user] = $this->checkout();

        Notification::assertSentTo($user, PesananDibuat::class);
    }

    public function test_email_pembayaran_diterima_terkirim_saat_lunas(): void
    {
        Notification::fake();
        [$user, $pesanan] = $this->checkout();

        $this->actingAs($user)->post(route('pesanan.bayar', $pesanan->kode));

        Notification::assertSentTo($user, PembayaranDiterima::class);
    }

    public function test_email_dikirim_berisi_resi_saat_status_dikirim(): void
    {
        Notification::fake();
        [$user, $pesanan] = $this->checkout();
        $admin = User::create(['name' => 'Admin', 'email' => 'a@x.com', 'password' => 'password', 'role' => 'admin']);

        $this->actingAs($admin)->patch(route('admin.pesanan.update', $pesanan), [
            'status' => 'dikirim', 'resi' => 'JNE99887766',
        ]);

        Notification::assertSentTo($user, PesananDikirim::class,
            fn (PesananDikirim $n) => $n->pesanan->resi === 'JNE99887766');
    }
}
