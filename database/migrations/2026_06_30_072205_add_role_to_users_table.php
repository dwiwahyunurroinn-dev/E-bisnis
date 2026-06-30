<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pelanggan'])->default('pelanggan')->after('email');
            $table->string('telepon', 25)->nullable()->after('role');
            $table->boolean('aktif')->default(true)->after('telepon');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'telepon', 'aktif']);
        });
    }
};
