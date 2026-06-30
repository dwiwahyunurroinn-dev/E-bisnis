<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');   // 1-5
            $table->text('komentar')->nullable();
            $table->timestamps();

            $table->unique(['produk_id', 'user_id']); // 1 ulasan per user per produk
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasans');
    }
};
