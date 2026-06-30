<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('slug', 180)->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_bundle', 12, 2);  // harga paket (lebih murah dari total)
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('bundle_produk', function (Blueprint $table) {
            $table->foreignId('bundle_id')->constrained('bundles')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->primary(['bundle_id', 'produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_produk');
        Schema::dropIfExists('bundles');
    }
};
