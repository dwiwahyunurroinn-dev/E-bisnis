<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obrolan_pesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obrolan_id')->constrained('obrolans')->cascadeOnDelete();
            // pelanggan, bot, atau admin.
            $table->string('pengirim', 15);
            $table->text('pesan');
            $table->timestamps();

            $table->index(['obrolan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obrolan_pesans');
    }
};
