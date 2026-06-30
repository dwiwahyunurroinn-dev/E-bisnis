<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

// Katalog
Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/{produk}', [ProdukController::class, 'show'])->name('produk.show');

// Keranjang
Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
Route::post('/keranjang/{produk}', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
Route::patch('/keranjang/{produk}', [KeranjangController::class, 'ubah'])->name('keranjang.ubah');
Route::delete('/keranjang/{produk}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Pesanan
Route::get('/pesanan/{kode}', [PesananController::class, 'show'])->name('pesanan.show');
Route::post('/pesanan/{kode}/bayar', [PesananController::class, 'bayar'])->name('pesanan.bayar');
