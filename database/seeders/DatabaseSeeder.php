<?php

namespace Database\Seeders;

use App\Models\Alamat;
use App\Models\Bundle;
use App\Models\Produk;
use App\Models\Promo;
use App\Models\Ulasan;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin awal — kredensial dari .env (ADMIN_EMAIL / ADMIN_PASSWORD).
        User::updateOrCreate(
            ['email' => config('toko.admin_email')],
            ['name' => 'Admin '.config('toko.nama'), 'password' => config('toko.admin_password'), 'role' => 'admin', 'telepon' => '0812-0000-0001'],
        );

        // Akun demo pelanggan hanya untuk lingkungan non-produksi.
        if (! app()->isProduction()) {
            $budiUser = User::updateOrCreate(
                ['email' => 'pelanggan@contoh.com'],
                ['name' => 'Budi Pelanggan', 'password' => 'password', 'role' => 'pelanggan', 'telepon' => '0812-0000-0002'],
            );
            Alamat::updateOrCreate(
                ['user_id' => $budiUser->id, 'label' => 'Rumah'],
                ['penerima' => 'Budi Pelanggan', 'telepon' => '0812-0000-0002', 'kota' => 'Yogyakarta', 'alamat_lengkap' => 'Jl. Kaliurang KM 5 No. 10', 'kode_pos' => '55281', 'utama' => true],
            );
        }

        // Promo slider
        Promo::insert([
            ['judul' => 'Diskon Spesial Furnitur Daur Ulang!', 'subjudul' => 'Hemat hingga 30% untuk koleksi meja & rak pilihan.', 'label' => 'Promo 30%', 'warna' => '#2c8064', 'tautan' => '/', 'urutan' => 1, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Gratis Ongkir se-Pulau Jawa', 'subjudul' => 'Belanja minimal Rp1.000.000 dan nikmati gratis ongkir.', 'label' => 'Gratis Ongkir', 'warna' => '#1f6b52', 'tautan' => '/', 'urutan' => 2, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Bundle Hemat: Meja + Rak Buku', 'subjudul' => 'Beli paket dan dapatkan harga lebih murah.', 'label' => 'Bundle', 'warna' => '#3aa17e', 'tautan' => '/', 'urutan' => 3, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->call(KatalogSeeder::class);
        $this->call(FaqSeeder::class);

        // Voucher demo
        Voucher::insert([
            ['kode' => 'ECOHEMAT10', 'tipe' => 'persen', 'nilai' => 10, 'maks_potongan' => 100000, 'min_belanja' => 500000, 'kuota' => 100, 'terpakai' => 0, 'kadaluarsa' => now()->addMonths(2), 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['kode' => 'GRATIS50K', 'tipe' => 'nominal', 'nilai' => 50000, 'maks_potongan' => null, 'min_belanja' => 1000000, 'kuota' => null, 'terpakai' => 0, 'kadaluarsa' => null, 'aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Bundle demo: Meja Belajar + Rak Buku
        $meja = Produk::where('slug', 'meja-belajar-jati-reclaimed')->first();
        $rak  = Produk::where('slug', 'rak-buku-4-tingkat-palet')->first();
        if ($meja && $rak) {
            $bundle = Bundle::create([
                'nama' => 'Paket Kerja Produktif: Meja Belajar + Rak Buku',
                'slug' => 'paket-kerja-produktif',
                'deskripsi' => 'Lengkapi ruang kerja Anda dengan harga lebih hemat.',
                'harga_bundle' => 1900000, // normal 1.250.000 + 890.000 = 2.140.000
                'aktif' => true,
            ]);
            $bundle->produk()->attach([$meja->id => ['jumlah' => 1], $rak->id => ['jumlah' => 1]]);
        }

        // Ulasan demo
        $budi = User::where('email', 'pelanggan@contoh.com')->first();
        if ($budi && $meja) {
            Ulasan::updateOrCreate(
                ['produk_id' => $meja->id, 'user_id' => $budi->id],
                ['rating' => 5, 'komentar' => 'Kualitas kayunya kokoh dan finishing rapi. Sangat puas!'],
            );
        }
    }
}
