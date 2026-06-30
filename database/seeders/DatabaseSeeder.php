<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin default
        User::updateOrCreate(
            ['email' => 'admin@ecocraft.id'],
            ['name' => 'Admin Eco Craft', 'password' => 'password', 'role' => 'admin', 'telepon' => '0812-0000-0001'],
        );

        // Pelanggan contoh
        User::updateOrCreate(
            ['email' => 'pelanggan@contoh.com'],
            ['name' => 'Budi Pelanggan', 'password' => 'password', 'role' => 'pelanggan', 'telepon' => '0812-0000-0002'],
        );

        // Promo slider
        Promo::insert([
            ['judul' => 'Diskon Spesial Furnitur Daur Ulang!', 'subjudul' => 'Hemat hingga 30% untuk koleksi meja & rak pilihan.', 'label' => 'Promo 30%', 'warna' => '#2c8064', 'tautan' => '/', 'urutan' => 1, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Gratis Ongkir se-Pulau Jawa', 'subjudul' => 'Belanja minimal Rp1.000.000 dan nikmati gratis ongkir.', 'label' => 'Gratis Ongkir', 'warna' => '#1f6b52', 'tautan' => '/', 'urutan' => 2, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Bundle Hemat: Meja + Rak Buku', 'subjudul' => 'Beli paket dan dapatkan harga lebih murah.', 'label' => 'Bundle', 'warna' => '#3aa17e', 'tautan' => '/', 'urutan' => 3, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->call(KatalogSeeder::class);
    }
}
