# Eco Craft — E-Commerce Furnitur Kayu Daur Ulang

Aplikasi e-commerce (Laravel 13 + MySQL) untuk UMKM furnitur berbahan kayu daur ulang.
Mencakup **storefront** (etalase belanja) dan **panel admin** lengkap.

## Akun demo

| Peran | Email | Password |
|-------|-------|----------|
| Admin | `admin@ecocraft.id` | `password` |
| Pelanggan | `pelanggan@contoh.com` | `password` |

Panel admin: buka `/admin` setelah login sebagai admin.

## Status pengembangan

| Fase | Fitur | Status |
|------|-------|--------|
| **1** | Database inventory (produk, bahan baku many-to-many, pesanan) + **trigger pengurang stok otomatis** saat pesanan `lunas` | ✅ Selesai |
| **1** | Galeri produk + UI marketplace modern (hijau soft, animasi) + lazy loading + filter + pencarian | ✅ Selesai |
| **2** | Keranjang + single-page checkout + kalkulator ongkir + pembayaran (memicu trigger stok) | ✅ Selesai |
| **3** | Auth (login/register, role admin & pelanggan) + integrasi **Midtrans Snap** (gated) | ✅ Selesai |
| **3** | **Panel admin**: dashboard+grafik, CRUD produk (upload gambar lokal), pesanan, stok, pelanggan, user, promo/slider, laporan periode + ekspor CSV, log aktivitas | ✅ Selesai |
| **3** | Storefront: rebrand **Eco Craft** + Instagram, live promo slider, animasi modern | ✅ Selesai |
| **4** | Loyalty: voucher generator + bundle offers | ⬜ Rencana |
| **5** | Chatbot CRM (FAQ otomatis + handoff ke admin) | ⬜ Rencana |
| **6** | Caching & optimasi query untuk traffic tinggi | ⬜ Rencana |

### Panel admin (`/admin`)

Dashboard (statistik + grafik penjualan 7 hari), manajemen **Produk** (CRUD +
upload gambar dari komputer), **Pesanan** (ubah status), **Stok** (produk &
bahan baku), **Pelanggan**, **User** (role), **Promo/Slider**, **Laporan**
(filter periode + ekspor CSV), dan **Log Aktivitas**. Akses dijaga middleware
`admin` (role-based).

### Pembayaran (Midtrans)

Isi `MIDTRANS_SERVER_KEY` & `MIDTRANS_CLIENT_KEY` di `.env` untuk mengaktifkan
pembayaran asli (Snap) + webhook di `/midtrans/webhook`. Bila kosong, aplikasi
memakai mode **simulasi** (tombol bayar manual). Lihat `app/Services/PaymentService.php`.

### Alur belanja (Fase 2)

`Galeri → Detail produk → Tambah ke Keranjang → Checkout (alamat + pilih kurir +
ongkir otomatis) → Buat Pesanan → Bayar (simulasi) → status 'lunas' → stok berkurang`

- **Keranjang**: berbasis session (`app/Services/CartService.php`), guest-friendly.
- **Ongkir**: `app/Services/OngkirService.php` — simulasi berbasis berat, format mirip
  RajaOngkir sehingga mudah diganti panggilan API asli.
- **Checkout**: guest checkout (buat/cari user via email), satu halaman.
- **Pembayaran**: tombol simulasi mengubah status → `lunas`. Di MySQL/MariaDB
  pengurangan stok dilakukan trigger DB; di SQLite dev dilakukan di aplikasi.

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
