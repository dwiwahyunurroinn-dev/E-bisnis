<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alamat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('label', 40)->nullable();     // "Rumah", "Kantor"
            $table->string('penerima', 120);
            $table->string('telepon', 25);
            $table->string('provinsi', 80)->nullable();
            $table->string('kota', 80)->nullable();
            $table->unsignedInteger('kota_id')->nullable(); // id kota utk RajaOngkir
            $table->string('kecamatan', 80)->nullable();
            $table->text('alamat_lengkap');
            $table->string('kode_pos', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alamat');
    }
};
