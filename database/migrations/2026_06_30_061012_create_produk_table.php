<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori');
            $table->string('nama', 150);
            $table->string('slug', 180)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('dimensi', 100)->nullable();          // "120 x 60 x 75 cm"
            $table->decimal('harga', 12, 2)->default(0);
            $table->unsignedInteger('berat_gram')->default(1000); // utk hitung ongkir
            $table->integer('stok')->default(0);
            $table->string('gambar', 255)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
