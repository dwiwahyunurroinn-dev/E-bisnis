# Panduan Deploy — aaPanel (VPS + domain sendiri)

Untuk server dengan **aaPanel** (Apache/Nginx), PHP 8.3, akses Terminal penuh.
Ganti `DOMAIN` di bawah dengan domain/subdomain Anda (mis. `namaanda.sisfo.my.id`).

> Urutan besar: buat site → buat database → longgarkan PHP → ambil kode →
> `.env` + composer + artisan → arahkan ke `/public` → rewrite Laravel → SSL.

---

## 1. Buat Website di aaPanel

1. Menu **Website** → **Add site**.
2. **Domain**: isi domain/subdomain Anda, mis. `namaanda.sisfo.my.id`.
3. Bagian **Database**: pilih **MySQL** → aaPanel otomatis membuatkan database.
   Catat **Database name, User, Password** yang muncul (dipakai di `.env`).
4. **PHP version**: pilih **PHP-8.3**.
5. Klik **Submit**.

Root situs biasanya di `/www/wwwroot/namaanda.sisfo.my.id`.

## 2. Longgarkan fungsi PHP yang diblokir aaPanel ⚠️ (paling sering bikin gagal)

aaPanel secara default memblokir fungsi yang **dibutuhkan Composer & Laravel**.
Wajib dibuka dulu:

1. Menu **App Store** → cari **PHP-8.3** → klik **Setting**.
2. Tab **Disabled functions**.
3. **Hapus** fungsi berikut dari daftar (bila ada):
   `proc_open`, `putenv`, `pcntl_signal`, `pcntl_alarm`, `symlink`, `exec`, `shell_exec`, `readlink`
4. Save.

Di tab **Install extensions** pastikan aktif: `fileinfo`, `redis`(opsional), `exif`,
`gd` (untuk crop foto), `pdo_mysql`. Biasanya `gd` & `pdo_mysql` sudah aktif.

## 3. Ambil kode project

**Cara A — git clone (disarankan).** Menu **Terminal** di aaPanel:

```bash
cd /www/wwwroot/namaanda.sisfo.my.id
# hapus file bawaan aaPanel agar folder bersih
rm -rf ./* ./.??*
git clone -b claude/ecommerce-continuation-0k9b88 https://github.com/dwiwahyunurroinn-dev/e-bisnis.git .
```

> Kalau repo Anda **privat**, git akan minta login. Termudah: buka GitHub →
> repo → Settings → General → Danger Zone → **Change visibility → Public**
> (aman untuk project tugas). Lalu ulangi `git clone` di atas.

**Cara B — upload ZIP.** Di GitHub: tombol **Code → Download ZIP**. Lalu di aaPanel
menu **Files** → masuk folder situs → **Upload** ZIP → **Unzip**. (composer tetap
perlu dijalankan di langkah 4.)

## 4. Install dependency + konfigurasi Laravel

Masih di **Terminal**, dari dalam folder situs. Pakai PHP 8.3 milik aaPanel:

```bash
cd /www/wwwroot/namaanda.sisfo.my.id
P=/www/server/php/83/bin/php

# Composer (kalau belum ada)
curl -sS https://getcomposer.org/installer | $P
$P composer.phar install --no-dev --optimize-autoloader

# Konfigurasi
cp .env.example .env
$P artisan key:generate
```

Lalu **edit `.env`** (menu Files → klik `.env` → Edit, atau `nano .env`):

```env
APP_NAME="Eco Craft"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://namaanda.sisfo.my.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_db_dari_langkah_1
DB_USERNAME=user_db_dari_langkah_1
DB_PASSWORD=password_db_dari_langkah_1

MAIL_MAILER=log

# Kredensial admin awal — ISI PASSWORD KUAT!
ADMIN_EMAIL=admin@namaanda.sisfo.my.id
ADMIN_PASSWORD=GantiPasswordKuat123!
```

Kemudian:

```bash
$P artisan migrate --seed --force
$P artisan storage:link
$P artisan config:cache
$P artisan route:cache
$P artisan view:cache
```

## 5. Arahkan situs ke folder `/public`

Laravel HARUS menunjuk ke subfolder `public`, bukan akar situs.

1. Menu **Website** → klik nama situs → **Site directory** (atau **Conf**).
2. Ubah **Running directory** menjadi **`/public`** → Save.

## 6. Aktifkan URL Rewrite Laravel

1. Masih di pengaturan situs → tab **URL rewrite** (Pseudo-static).
2. Pilih template **`laravel5`** dari dropdown → Save.
   (Ini membuat aturan rewrite agar semua URL diarahkan ke `index.php`.)

## 7. Perbaiki kepemilikan & izin folder

aaPanel menjalankan web sebagai user **www**. Di **Terminal**:

```bash
cd /www/wwwroot/namaanda.sisfo.my.id
chown -R www:www .
chmod -R 775 storage bootstrap/cache
```

## 8. Pasang SSL (HTTPS)

1. Menu **Website** → situs Anda → tab **SSL**.
2. Pilih **Let's Encrypt** → centang domain → **Apply**.
3. Aktifkan **Force HTTPS**.

## 9. Cron untuk auto-expire pesanan

Menu **Cron** → Add task:
- Type: **Shell Script**, jadwal **1 minute**.
- Script:
  ```
  cd /www/wwwroot/namaanda.sisfo.my.id && /www/server/php/83/bin/php artisan schedule:run
  ```

## 10. Cek akhir (buka domain Anda) 🎓

- [ ] `https://namaanda.sisfo.my.id` menampilkan toko (gembok hijau)
- [ ] Buka URL ngawur → muncul 404 biasa, BUKAN halaman error berisi kode (`APP_DEBUG=false`)
- [ ] Login admin pakai `ADMIN_EMAIL` / `ADMIN_PASSWORD` dari `.env`
- [ ] Upload foto produk tampil (berarti `storage:link` sukses)
- [ ] Chatbot menjawab; checkout jalan

---

## Kalau muncul error

- **500 Internal Server Error / halaman putih** → cek `storage/logs/laravel.log`
  (menu Files). Umumnya: izin folder (ulangi langkah 7) atau `APP_KEY` kosong
  (`$P artisan key:generate` lalu `$P artisan config:cache`).
- **"could not find driver"** → aktifkan ekstensi `pdo_mysql` (langkah 2).
- **Composer/artisan error `proc_open()/putenv() disabled`** → langkah 2 belum
  dilakukan; hapus fungsi itu dari Disabled functions.
- **Foto/logo tak muncul** → `storage:link` belum jalan, atau folder `public/storage`
  bukan milik `www` (ulangi langkah 7).
- **Ganti kode nanti**: `git pull` lalu ulangi
  `composer install --no-dev`, `artisan migrate`, dan ketiga `*:cache`.
