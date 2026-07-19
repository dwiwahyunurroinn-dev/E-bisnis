<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ObrolanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\UlasanController;
use Illuminate\Support\Facades\Route;

// ---------- Storefront ----------
Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/{produk}', [ProdukController::class, 'show'])->name('produk.show');

// Keranjang
Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
Route::post('/keranjang/{produk}', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
Route::patch('/keranjang/{produk}', [KeranjangController::class, 'ubah'])->name('keranjang.ubah');
Route::delete('/keranjang/{produk}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');

// Voucher & bundle (diskon)
Route::post('/voucher', [DiskonController::class, 'pasangVoucher'])->name('voucher.pasang');
Route::delete('/voucher', [DiskonController::class, 'lepasVoucher'])->name('voucher.lepas');
Route::post('/bundle/{bundle}', [DiskonController::class, 'tambahBundle'])->name('bundle.tambah');

// Ulasan produk (wajib login)
Route::post('/produk/{produk}/ulasan', [UlasanController::class, 'store'])->middleware('auth')->name('ulasan.store');

// Live chat / chatbot FAQ (guest-friendly, seperti keranjang).
// Dibatasi throttle agar tamu tidak bisa membanjiri chat/DB.
Route::get('/obrolan', [ObrolanController::class, 'muat'])->middleware('throttle:60,1')->name('obrolan.muat');
Route::post('/obrolan', [ObrolanController::class, 'kirim'])->middleware('throttle:20,1')->name('obrolan.kirim');
Route::post('/obrolan/admin', [ObrolanController::class, 'mintaAdmin'])->middleware('throttle:5,1')->name('obrolan.admin');

// Checkout & pesanan WAJIB LOGIN (standar marketplace).
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/pesanan/{kode}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::post('/pesanan/{kode}/bayar', [PesananController::class, 'bayar'])->name('pesanan.bayar');
    Route::post('/pesanan/{kode}/batal', [PesananController::class, 'batal'])->name('pesanan.batal');
    Route::post('/pesanan/{kode}/terima', [PesananController::class, 'terima'])->name('pesanan.terima');
});

// Webhook Midtrans (server-to-server, tanpa login/CSRF).
Route::post('/midtrans/webhook', [PesananController::class, 'webhook'])->name('midtrans.webhook');

// ---------- Auth ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');

    // Lupa / reset kata sandi
    Route::get('/lupa-password', [AuthController::class, 'showLupaPassword'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'kirimLinkReset'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- Area pelanggan (login) ----------
Route::middleware('auth')->group(function () {
    Route::get('/akun', [AkunController::class, 'dashboard'])->name('akun.dashboard');
    Route::get('/akun/profil', [AkunController::class, 'profil'])->name('akun.profil');
    Route::patch('/akun/profil', [AkunController::class, 'updateProfil'])->name('akun.profil.update');
    Route::patch('/akun/password', [AkunController::class, 'updatePassword'])->name('akun.password');
    Route::get('/akun/pesanan', [AkunController::class, 'pesanan'])->name('akun.pesanan');

    // Address book
    Route::get('/akun/alamat', [AlamatController::class, 'index'])->name('akun.alamat');
    Route::post('/akun/alamat', [AlamatController::class, 'store'])->name('akun.alamat.store');
    Route::patch('/akun/alamat/{alamat}', [AlamatController::class, 'update'])->name('akun.alamat.update');
    Route::delete('/akun/alamat/{alamat}', [AlamatController::class, 'destroy'])->name('akun.alamat.destroy');
    Route::post('/akun/alamat/{alamat}/utama', [AlamatController::class, 'jadikanUtama'])->name('akun.alamat.utama');

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{notifikasi}', [NotifikasiController::class, 'buka'])->name('notifikasi.buka');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
});

// ---------- Admin ----------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('produk', Admin\ProdukController::class)->except('show');
    Route::resource('promo', Admin\PromoController::class)->except('show');
    Route::resource('voucher', Admin\VoucherController::class)->except('show');
    Route::resource('bundle', Admin\BundleController::class)->except('show');
    Route::resource('faq', Admin\FaqController::class)->except('show');
    Route::resource('user', Admin\UserController::class)->except('show');

    Route::get('obrolan', [Admin\ObrolanController::class, 'index'])->name('obrolan.index');
    Route::get('obrolan/{obrolan}', [Admin\ObrolanController::class, 'show'])->name('obrolan.show');
    Route::post('obrolan/{obrolan}/balas', [Admin\ObrolanController::class, 'balas'])->name('obrolan.balas');
    Route::post('obrolan/{obrolan}/selesai', [Admin\ObrolanController::class, 'selesai'])->name('obrolan.selesai');

    Route::get('pesanan', [Admin\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('pesanan/{pesanan}', [Admin\PesananController::class, 'show'])->name('pesanan.show');
    Route::patch('pesanan/{pesanan}', [Admin\PesananController::class, 'update'])->name('pesanan.update');

    Route::get('stok', [Admin\StokController::class, 'index'])->name('stok.index');
    Route::patch('stok/produk/{produk}', [Admin\StokController::class, 'updateProduk'])->name('stok.produk');
    Route::patch('stok/bahan/{bahan}', [Admin\StokController::class, 'updateBahan'])->name('stok.bahan');

    Route::get('pelanggan', [Admin\PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('pelanggan/{pelanggan}', [Admin\PelangganController::class, 'show'])->name('pelanggan.show');

    Route::get('laporan', [Admin\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/ekspor', [Admin\LaporanController::class, 'ekspor'])->name('laporan.ekspor');

    Route::get('log', [Admin\ActivityLogController::class, 'index'])->name('log.index');

    Route::get('pengaturan', [Admin\PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('pengaturan', [Admin\PengaturanController::class, 'update'])->name('pengaturan.update');
});
