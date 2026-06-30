<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->string('subjudul', 200)->nullable();
            $table->string('label', 60)->nullable();        // mis. "Diskon 30%"
            $table->string('gambar', 255)->nullable();       // banner upload
            $table->string('warna', 30)->default('#2c8064'); // warna dasar slide
            $table->string('tautan', 255)->nullable();       // URL tujuan tombol
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
