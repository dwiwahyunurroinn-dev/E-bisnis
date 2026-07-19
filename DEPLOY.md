# Panduan Deploy — Shared Hosting cPanel (Rumahweb)

Panduan ini untuk hosting cPanel seperti Rumahweb. Kebutuhan minimal paket hosting:
**PHP 8.3+**, MySQL/MariaDB, akses **Terminal** atau **SSH** (ada di cPanel Rumahweb),
dan Composer (biasanya sudah tersedia; cek dengan `composer --version` di Terminal).

> Ringkasan alur: buat database → upload kode → composer install → isi `.env` produksi
> → migrasi → arahkan domain ke folder `public/` → cron & SSL.

---

## 1. Buat database MySQL di cPanel

1. Buka **cPanel → MySQL® Databases**.
2. Buat database, misal `usercpanel_ebisnis`.
3. Buat user database + password kuat, misal `usercpanel_eco`.
4. **Add User To Database** → beri **ALL PRIVILEGES**.
5. Catat: nama database, username, password (dipakai di `.env` nanti).

## 2. Upload kode

**Cara A — Git (disarankan).** Buka **cPanel → Terminal**:

```bash
cd ~
git clone -b claude/ecommerce-continuation-0k9b88 https://github.com/dwiwahyunurroinn-dev/e-bisnis.git
cd e-bisnis
```

**Cara B — Zip.** Zip folder project di laptop (TANPA folder `vendor` dan `node_modules`),
upload via **File Manager** ke home direktori (bukan `public_html`), lalu extract.

> Penting: letakkan project di **luar** `public_html` (mis. `~/e-bisnis`) agar file
> `.env` dan kode tidak bisa diakses publik.

## 3. Install dependency

Di Terminal cPanel, dari dalam folder project:

```bash
composer install --no-dev --optimize-autoloader
```

Jika `composer` tidak ada, coba `/usr/local/bin/ea-php83 /opt/cpanel/composer/bin/composer`
atau hubungi support Rumahweb untuk path Composer.

## 4. Konfigurasi `.env` produksi

```bash
cp .env.example .env
php artisan key:generate
```

Lalu edit `.env` (File Manager → Edit, atau `nano .env`). Nilai penting:

```env
APP_NAME="Eco Craft"
APP_ENV=production
APP_DEBUG=false          # WAJIB false! Kalau true, error menampilkan isi kode ke publik
APP_URL=https://domainanda.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=usercpanel_ebisnis
DB_USERNAME=usercpanel_eco
DB_PASSWORD=passwordkuat

# Email (untuk fitur lupa password) — sendmail tersedia di hosting cPanel
MAIL_MAILER=sendmail
MAIL_FROM_ADDRESS=no-reply@domainanda.com
MAIL_FROM_NAME="Eco Craft"

# Kredensial admin awal — WAJIB isi password kuat SEBELUM migrate --seed
ADMIN_EMAIL=admin@domainanda.com
ADMIN_PASSWORD=GantiDenganPasswordKuat123!

# Kosongkan = mode simulasi pembayaran. Isi bila punya akun Midtrans (sandbox/production).
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
```

## 5. Migrasi database + storage link

```bash
php artisan migrate --seed        # sekali saat pertama deploy
php artisan storage:link          # agar upload gambar produk tampil
```

> `--seed` mengisi katalog contoh + akun admin sesuai `ADMIN_EMAIL`/`ADMIN_PASSWORD`
> di `.env` (pastikan sudah diisi password kuat pada Langkah 4). Akun demo
> pelanggan otomatis TIDAK dibuat saat `APP_ENV=production`.

## 6. Arahkan domain ke folder `public/`

Keamanan Laravel mengharuskan document root menunjuk ke subfolder `public/`, bukan akar project.

**Jika domain utama** (`public_html`) — di Rumahweb ada 2 cara:

- **Cara termudah**: buka **cPanel → Domains**, klik domain Anda → ubah
  **Document Root** menjadi `/home/usercpanel/e-bisnis/public`. (Rumahweb mengizinkan
  ini untuk addon domain; untuk domain utama bisa minta bantuan support via live chat.)
- **Alternatif** bila document root tidak bisa diubah: kosongkan `public_html`, lalu buat
  file `public_html/.htaccess` berisi:

  ```apache
  RewriteEngine On
  RewriteRule ^(.*)$ /e-bisnis-public/$1 [L]
  ```

  Cara ini rumit; lebih baik minta support ubah document root — gratis dan 5 menit.

## 7. Optimasi produksi

Setiap kali selesai deploy/update kode:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

(Bila mengubah `.env` setelah ini, jalankan `php artisan config:cache` lagi.)

## 8. Cron untuk scheduler (auto-expire pesanan)

Buka **cPanel → Cron Jobs**, tambah cron **setiap menit** (`* * * * *`):

```
cd /home/usercpanel/e-bisnis && php artisan schedule:run >> /dev/null 2>&1
```

Ini menjalankan pembatalan otomatis pesanan pending yang lewat batas bayar (tiap 10 menit).

## 9. SSL / HTTPS

Rumahweb menyediakan SSL gratis: **cPanel → SSL/TLS Status** → jalankan **AutoSSL**
untuk domain Anda. Pastikan `APP_URL` di `.env` memakai `https://`.

## 10. Checklist akhir sebelum dinilai 🎓

- [ ] `APP_DEBUG=false` — buka URL yang salah, pastikan muncul halaman 404 biasa, bukan halaman error berisi kode
- [ ] `ADMIN_PASSWORD` di `.env` sudah diisi password kuat (bukan `password`)
- [ ] Register akun baru → login → checkout → bayar (simulasi) → stok berkurang
- [ ] Lupa password → email masuk (cek folder spam)
- [ ] Chatbot menjawab FAQ; pertanyaan asing masuk ke menu Live Chat admin
- [ ] Admin isi resi → tampil di halaman pesanan pelanggan
- [ ] Upload gambar produk dari panel admin → gambar tampil (storage:link sudah jalan)

## Update kode di kemudian hari

```bash
cd ~/e-bisnis
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
