<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

// ---------- Storefront ----------
Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/{produk}', [ProdukController::class, 'show'])->name('produk.show');

// Keranjang
Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
Route::post('/keranjang/{produk}', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
Route::patch('/keranjang/{produk}', [KeranjangController::class, 'ubah'])->name('keranjang.ubah');
Route::delete('/keranjang/{produk}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');

// Checkout & pesanan
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/pesanan/{kode}', [PesananController::class, 'show'])->name('pesanan.show');
Route::post('/pesanan/{kode}/bayar', [PesananController::class, 'bayar'])->name('pesanan.bayar');
Route::post('/midtrans/webhook', [PesananController::class, 'webhook'])->name('midtrans.webhook');

// ---------- Auth ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- Admin ----------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('produk', Admin\ProdukController::class)->except('show');
    Route::resource('promo', Admin\PromoController::class)->except('show');
    Route::resource('user', Admin\UserController::class)->except('show');

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
});
