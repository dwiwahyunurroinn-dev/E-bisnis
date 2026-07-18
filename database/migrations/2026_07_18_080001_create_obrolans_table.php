<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obrolans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Sesi tamu (belum login) dikenali lewat token acak di session/cookie.
            $table->string('token_tamu', 40)->nullable()->unique();
            $table->string('nama', 100)->nullable();
            // bot: dijawab otomatis. menunggu_admin: butuh manusia. selesai: ditutup.
            $table->string('status', 20)->default('bot');
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('terakhir_pesan_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'terakhir_pesan_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obrolans');
    }
};
