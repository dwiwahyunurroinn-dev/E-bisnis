# KayuReclaimed — E-Commerce Furnitur Kayu Daur Ulang

Aplikasi e-commerce (Laravel 13 + MySQL) untuk UMKM furnitur berbahan kayu daur ulang.
Dibangun bertahap menyelesaikan 6 masalah bisnis. Repo ini berisi **Fase 1 (pondasi)**.

## Status pengembangan

| Fase | Fitur | Status |
|------|-------|--------|
| **1** | Database inventory (produk, bahan baku many-to-many, pesanan) + **trigger pengurang stok otomatis** saat pesanan `lunas` | ✅ Selesai |
| **1** | Galeri produk earth-tone + lazy loading + filter kategori + pencarian | ✅ Selesai |
| **2** | Single-page checkout + kalkulator ongkir (RajaOngkir) + payment gateway (Midtrans/Xendit) | ⏳ Berikutnya |
| **3** | Loyalty program: voucher generator + bundle offers | ⬜ Rencana |
| **4** | Chatbot CRM (FAQ otomatis + handoff ke admin) | ⬜ Rencana |
| **5** | Caching & optimasi query untuk traffic tinggi | ⬜ Rencana |

## Struktur utama

```
app/Models/             Produk, Kategori, BahanBaku, Pesanan, DetailPesanan, Alamat
app/Http/Controllers/   ProdukController (galeri & detail)
database/migrations/    skema + trigger MySQL (trg_kurangi_stok_produk)
database/seeders/       KatalogSeeder (data contoh furnitur)
database/sql-reference/ schema.sql & seed.sql versi SQL murni (referensi)
resources/views/        layouts/app + produk/index + produk/show
```

## Cara menjalankan

### Opsi A — MySQL (rekomendasi, trigger aktif)
```bash
cp .env.example .env
# isi DB_DATABASE=ebisnis dan kredensial MySQL di .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Opsi B — SQLite (cepat, tanpa server MySQL; trigger dilewati)
```bash
cp .env.example .env
# ubah DB_CONNECTION=sqlite
touch database/database.sqlite
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Buka http://127.0.0.1:8000

## Cara kerja trigger stok real-time (Fase 1)

Saat status `pesanan` berubah menjadi `lunas`, trigger MySQL otomatis:
1. Mengurangi `stok` di tabel `produk` sesuai item pesanan.
2. Mengurangi `stok` `bahan_baku` sesuai komposisi (relasi many-to-many `produk_bahan_baku`).

> Catatan: trigger hanya berjalan pada koneksi MySQL/MariaDB. Pada SQLite (dev) trigger dilewati,
> sehingga pengurangan stok di dev SQLite perlu ditangani di layer aplikasi nanti.

## Langkah berikutnya (Fase 2)

1. Auth pelanggan (register/login) — gunakan Laravel Breeze/Fortify.
2. Keranjang belanja (session/db).
3. Halaman single-page checkout: alamat → pilih kurir → ongkir otomatis → bayar.
4. Service `RajaOngkir` untuk hitung ongkir, `Midtrans` untuk pembayaran + webhook
   yang mengubah status pesanan menjadi `lunas` (memicu trigger stok).
