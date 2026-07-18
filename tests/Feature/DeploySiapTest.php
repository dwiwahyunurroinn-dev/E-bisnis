<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\User;
use App\Notifications\ResetPasswordIndo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class DeploySiapTest extends TestCase
{
    use RefreshDatabase;

    /* ---------------- Lupa / reset kata sandi ---------------- */

    public function test_halaman_lupa_password_tampil(): void
    {
        $this->get(route('password.request'))->assertOk()->assertSee('Lupa Kata Sandi');
    }

    public function test_kirim_link_reset_membuat_token_dan_email(): void
    {
        Notification::fake();
        $user = User::create(['name' => 'Budi', 'email' => 'budi@x.com', 'password' => 'password']);

        $this->post(route('password.email'), ['email' => 'budi@x.com'])->assertRedirect();

        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'budi@x.com']);
        Notification::assertSentTo($user, ResetPasswordIndo::class);
    }

    public function test_reset_password_mengubah_kata_sandi(): void
    {
        $user = User::create(['name' => 'Budi', 'email' => 'budi@x.com', 'password' => 'lama-123']);
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token, 'email' => 'budi@x.com',
            'password' => 'baru-456', 'password_confirmation' => 'baru-456',
        ])->assertRedirect(route('login'));

        $this->assertTrue(auth()->attempt(['email' => 'budi@x.com', 'password' => 'baru-456']));
    }

    /* ---------------- Resi pengiriman ---------------- */

    public function test_admin_mengisi_resi_saat_status_dikirim(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'a@x.com', 'password' => 'password', 'role' => 'admin']);
        $user = User::create(['name' => 'Budi', 'email' => 'b@x.com', 'password' => 'password']);
        $pesanan = Pesanan::create(['kode' => 'INV-1', 'user_id' => $user->id, 'penerima' => 'B',
            'telepon' => '08', 'kota' => 'X', 'alamat_lengkap' => 'Y',
            'subtotal' => 100000, 'ongkir' => 0, 'total' => 100000, 'status' => 'diproses']);

        $this->actingAs($admin)->patch(route('admin.pesanan.update', $pesanan), [
            'status' => 'dikirim', 'resi' => 'JNE1234567890',
        ])->assertRedirect();

        $this->assertSame('JNE1234567890', $pesanan->fresh()->resi);
        $this->assertDatabaseHas('notifikasis', ['user_id' => $user->id, 'tipe' => 'status']);

        // Pelanggan melihat resinya di halaman pesanan.
        $this->actingAs($user)->get(route('pesanan.show', $pesanan->kode))->assertSee('JNE1234567890');
    }

    /* ---------------- Cache katalog (Fase 6) ---------------- */

    public function test_cache_nav_kategori_terhapus_saat_kategori_berubah(): void
    {
        Kategori::create(['nama' => 'Meja', 'slug' => 'meja']);
        $this->get(route('produk.index'))->assertSee('Meja'); // isi cache

        Kategori::create(['nama' => 'Kursi Unik', 'slug' => 'kursi-unik']);
        $this->get(route('produk.index'))->assertSee('Kursi Unik'); // cache harus segar
    }

    public function test_cache_database_store_dua_request_tidak_error(): void
    {
        // Regresi: store database menserialisasi objek; request kedua membaca
        // hasil unserialize (dibatasi cache.serializable_classes) dan harus tetap utuh.
        config(['cache.default' => 'database']);
        Kategori::create(['nama' => 'Meja', 'slug' => 'meja']);

        $this->get(route('produk.index'))->assertOk();           // tulis cache
        $this->get(route('produk.index'))->assertOk()->assertSee('Meja'); // baca cache
    }

    /* ---------------- Rate limiting ---------------- */

    public function test_login_dibatasi_setelah_percobaan_beruntun(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('login'), ['email' => 'x@x.com', 'password' => 'salah']);
        }

        $this->post(route('login'), ['email' => 'x@x.com', 'password' => 'salah'])
            ->assertStatus(429);
    }
}
