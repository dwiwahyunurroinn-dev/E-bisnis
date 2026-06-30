<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->enum('tipe', ['persen', 'nominal'])->default('persen');
            $table->decimal('nilai', 12, 2);              // persen (mis 10) atau nominal (rupiah)
            $table->decimal('maks_potongan', 12, 2)->nullable(); // batas potongan utk tipe persen
            $table->decimal('min_belanja', 12, 2)->default(0);
            $table->unsignedInteger('kuota')->nullable(); // null = tak terbatas
            $table->unsignedInteger('terpakai')->default(0);
            $table->date('kadaluarsa')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
