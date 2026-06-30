<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'a@a.com', 'password' => 'password', 'role' => 'admin']);
    }

    private function pelanggan(): User
    {
        return User::create(['name' => 'Cust', 'email' => 'c@c.com', 'password' => 'password', 'role' => 'pelanggan']);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_pelanggan_dilarang_masuk_admin(): void
    {
        $this->actingAs($this->pelanggan())->get('/admin')->assertForbidden();
    }

    public function test_admin_bisa_buka_dashboard(): void
    {
        $this->actingAs($this->admin())->get('/admin')->assertStatus(200)->assertSee('Dashboard');
    }

    public function test_admin_bisa_membuat_produk(): void
    {
        $kategori = Kategori::create(['nama' => 'Meja', 'slug' => 'meja']);

        $this->actingAs($this->admin())->post(route('admin.produk.store'), [
            'nama'        => 'Produk Baru',
            'kategori_id' => $kategori->id,
            'status'      => 'aktif',
            'harga'       => 100000,
            'stok'        => 5,
            'berat_gram'  => 2000,
        ])->assertRedirect(route('admin.produk.index'));

        $this->assertDatabaseHas('produk', ['nama' => 'Produk Baru', 'slug' => 'produk-baru']);
    }

    public function test_login_dan_logout_pelanggan(): void
    {
        $this->pelanggan();

        $this->post('/login', ['email' => 'c@c.com', 'password' => 'password'])
            ->assertRedirect(route('produk.index'));
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect();
        $this->assertGuest();
    }
}
