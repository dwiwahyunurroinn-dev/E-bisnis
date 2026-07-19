# Eco Craft — E-Commerce Furnitur Kayu Daur Ulang

Aplikasi e-commerce (Laravel 13 + MySQL) untuk UMKM furnitur berbahan kayu daur ulang.
Mencakup **storefront** (etalase belanja) dan **panel admin** lengkap.

## Akun demo

| Peran | Email | Password |
|-------|-------|----------|
| Admin | `admin@ecocraft.id` | `password` (dev default) |
| Pelanggan | `pelanggan@contoh.com` | `password` (hanya non-produksi) |

> Kredensial admin diatur via `ADMIN_EMAIL` / `ADMIN_PASSWORD` di `.env` —
> **wajib diganti sebelum seeding di produksi**. Akun demo pelanggan tidak
> dibuat sama sekali saat `APP_ENV=production`.

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
| **4** | Loyalty: voucher generator + bundle offers, ulasan produk, poin & tier membership | ✅ Selesai |
| **4** | Standar marketplace: checkout wajib login, dashboard akun (profil, pesanan, buku alamat), notifikasi, auto-expire pesanan pending | ✅ Selesai |
| **5** | **Chatbot CRM**: widget live chat di storefront, FAQ otomatis berbasis kata kunci, handoff ke admin (live chat) saat bot tak punya jawaban | ✅ Selesai |
| **6** | Caching katalog (kategori/promo/bundle, invalidasi otomatis) + rate limiting (login, chat) | ✅ Selesai |
| **+** | Siap deploy: lupa/reset password (email), nomor resi pengiriman, panduan hosting (`DEPLOY.md`) | ✅ Selesai |

### Panel admin (`/admin`)

Dashboard (statistik + grafik penjualan 7 hari), manajemen **Produk** (CRUD +
upload gambar dari komputer), **Pesanan** (ubah status + no. resi), **Stok**
(produk & bahan baku), **Pelanggan**, **User** (role), **Promo/Slider**,
**Voucher/Bundle**, **FAQ Chatbot & Live Chat**, **Laporan** (filter periode +
ekspor CSV), **Log Aktivitas**, dan **Pengaturan Toko** (nama, logo, WhatsApp,
Instagram, email, barcode QRIS, rekening bank — tersimpan di DB, menimpa
default `.env`). Akses dijaga middleware `admin` (role-based).

### Pembayaran (gaya marketplace)

Checkout menyediakan pilihan metode ala Tokopedia (`config/pembayaran.php`):

- **QRIS** — halaman bayar menampilkan kode QR (simulasi) + total.
- **Virtual Account m-banking** (BCA, BRI, BNI, Mandiri) — nomor VA deterministik
  per pesanan, tombol salin, dan panduan cara bayar (m-banking/ATM).
- **E-Wallet** (GoPay, OVO, DANA, ShopeePay).
- **COD** — pesanan langsung dikonfirmasi (stok berkurang), bayar tunai ke kurir;
  admin melihat tagihan tunai di detail pesanan.

Halaman bayar menampilkan **batas waktu + hitung mundur**. Mode default = simulasi
(tombol "Saya Sudah Bayar"). Isi `MIDTRANS_SERVER_KEY` & `MIDTRANS_CLIENT_KEY` di
`.env` untuk pembayaran asli via Midtrans Snap (+ webhook `/midtrans/webhook`) —
QRIS/VA/e-wallet ditangani Midtrans, COD tetap oleh aplikasi.

### Live chat & chatbot FAQ (Fase 5)

Widget bulat di kanan-bawah setiap halaman (guest maupun login) membuka panel chat:

1. Pesan pelanggan dicocokkan ke **FAQ** (`app/Services/ChatbotService.php`) lewat kata kunci
   (dikelola admin di `/admin/faq`) — jika cocok, bot langsung membalas.
2. Jika tidak ada yang cocok (atau pelanggan klik "Bicara dengan Admin"), percakapan
   ditandai `menunggu_admin`, admin mendapat notifikasi, dan bisa membalas manual di `/admin/obrolan`.
3. Tamu (belum login) tetap bisa chat — sesi dikenali lewat token acak di session, mirip keranjang.
4. Tombol "Via WhatsApp" tetap tersedia sebagai jalur alternatif ke admin.

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

## Caching & hardening (Fase 6)

- **Cache katalog** (`app/Services/KatalogCache.php`): nav kategori, promo slider, dan
  bundle di-cache 10 menit dan **di-invalidasi otomatis** saat admin mengubah
  produk/kategori/promo/bundle (model event di `AppServiceProvider`).
- **Rate limiting**: login (10/menit), register (5/menit), chatbot publik (kirim 20/menit)
  — melindungi dari brute-force & spam.
- **Lupa password**: tautan reset via email berbahasa Indonesia
  (`/lupa-password`; di server pakai `MAIL_MAILER=sendmail`, di dev `log`).
- **Resi pengiriman**: admin mengisi no. resi saat status *Dikirim*; tampil di halaman
  pesanan pelanggan beserta timeline status.

## Deploy ke hosting

Lihat **`DEPLOY.md`** untuk panduan langkah demi langkah deploy ke shared hosting
cPanel (Rumahweb dsb.): setup database, `.env` produksi, document root `public/`,
cron scheduler, dan SSL.
