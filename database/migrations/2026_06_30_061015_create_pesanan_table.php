<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();            // "INV-20260630-0001"
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('alamat_id')->constrained('alamat');
            $table->string('kurir', 20)->nullable();         // "jne","pos","tiki"
            $table->string('layanan', 40)->nullable();       // "REG","YES","OKE"
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('ongkir', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('metode_bayar', 40)->nullable();  // "midtrans","xendit"
            $table->enum('status', [
                'pending', 'lunas', 'diproses', 'dikirim', 'selesai', 'batal',
            ])->default('pending');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
