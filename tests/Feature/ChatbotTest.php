<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Notifikasi;
use App\Models\Obrolan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_bot_membalas_otomatis_saat_kata_kunci_cocok(): void
    {
        Faq::create([
            'pertanyaan' => 'Berapa lama pengiriman?',
            'jawaban' => 'Estimasi 2-5 hari kerja.',
            'kata_kunci' => 'ongkir,pengiriman,kirim',
            'aktif' => true,
        ]);

        $res = $this->postJson(route('obrolan.kirim'), ['pesan' => 'Berapa lama waktu pengiriman ke Jakarta?']);

        $res->assertOk()->assertJsonPath('status', 'bot');
        $pesan = $res->json('pesan');
        $this->assertSame('pelanggan', $pesan[0]['pengirim']);
        $this->assertSame('bot', $pesan[1]['pengirim']);
        $this->assertStringContainsString('2-5 hari', $pesan[1]['pesan']);
    }

    public function test_diteruskan_ke_admin_saat_bot_tidak_punya_jawaban(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@x.com', 'password' => 'password', 'role' => 'admin']);

        $res = $this->postJson(route('obrolan.kirim'), ['pesan' => 'Pertanyaan aneh yang tidak ada di FAQ']);

        $res->assertOk()->assertJsonPath('status', 'menunggu_admin');
        $this->assertDatabaseHas('obrolans', ['status' => 'menunggu_admin']);
        $this->assertDatabaseHas('notifikasis', ['user_id' => $admin->id, 'tipe' => 'chat']);
    }

    public function test_pelanggan_bisa_minta_bicara_dengan_admin_langsung(): void
    {
        $res = $this->postJson(route('obrolan.admin'));

        $res->assertOk()->assertJsonPath('status', 'menunggu_admin');
    }

    public function test_admin_bisa_membalas_dan_menyelesaikan_obrolan(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin2@x.com', 'password' => 'password', 'role' => 'admin']);
        $pelanggan = User::create(['name' => 'Budi', 'email' => 'budi@x.com', 'password' => 'password']);
        $obrolan = Obrolan::create(['user_id' => $pelanggan->id, 'status' => 'menunggu_admin', 'terakhir_pesan_at' => now()]);

        $this->actingAs($admin)->post(route('admin.obrolan.balas', $obrolan), ['pesan' => 'Halo, ada yang bisa dibantu?'])
            ->assertRedirect();

        $this->assertDatabaseHas('obrolan_pesans', ['obrolan_id' => $obrolan->id, 'pengirim' => 'admin']);

        $this->actingAs($admin)->post(route('admin.obrolan.selesai', $obrolan))->assertRedirect();
        $this->assertSame('selesai', $obrolan->fresh()->status);
    }

    public function test_multiadmin_semua_menerima_notifikasi_dan_bisa_membalas(): void
    {
        $admin1 = User::create(['name' => 'Admin1', 'email' => 'a1@x.com', 'password' => 'password', 'role' => 'admin']);
        $admin2 = User::create(['name' => 'Admin2', 'email' => 'a2@x.com', 'password' => 'password', 'role' => 'admin']);

        // Pelanggan bertanya sesuatu yang tak dikenal bot -> handoff.
        $this->postJson(route('obrolan.kirim'), ['pesan' => 'Pertanyaan khusus untuk manusia']);

        // KEDUA admin dapat notifikasi.
        $this->assertDatabaseHas('notifikasis', ['user_id' => $admin1->id, 'tipe' => 'chat']);
        $this->assertDatabaseHas('notifikasis', ['user_id' => $admin2->id, 'tipe' => 'chat']);

        // Keduanya bisa membuka inbox dan salah satunya membalas.
        $obrolan = Obrolan::first();
        $this->actingAs($admin1)->get(route('admin.obrolan.show', $obrolan))->assertOk();
        $this->actingAs($admin2)->post(route('admin.obrolan.balas', $obrolan), ['pesan' => 'Halo dari Admin2'])
            ->assertRedirect();
        $this->assertDatabaseHas('obrolan_pesans', ['obrolan_id' => $obrolan->id, 'pengirim' => 'admin']);
    }

    public function test_halaman_admin_faq_dan_obrolan_bisa_diakses(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin3@x.com', 'password' => 'password', 'role' => 'admin']);
        $obrolan = Obrolan::create(['nama' => 'Tamu', 'status' => 'menunggu_admin', 'terakhir_pesan_at' => now()]);

        $this->actingAs($admin)->get(route('admin.faq.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.faq.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.obrolan.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.obrolan.show', $obrolan))->assertOk();
    }
}
