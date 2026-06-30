<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alamat', function (Blueprint $table) {
            $table->boolean('utama')->default(false)->after('kode_pos');
        });
    }

    public function down(): void
    {
        Schema::table('alamat', function (Blueprint $table) {
            $table->dropColumn('utama');
        });
    }
};
