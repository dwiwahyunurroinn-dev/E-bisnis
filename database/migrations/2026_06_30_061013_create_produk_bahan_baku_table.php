<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Pivot many-to-many: produk <-> bahan_baku (komposisi/supply chain)
    public function up(): void
    {
        Schema::create('produk_bahan_baku', function (Blueprint $table) {
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2)->default(0); // bahan dipakai per 1 produk
            $table->primary(['produk_id', 'bahan_baku_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_bahan_baku');
    }
};
