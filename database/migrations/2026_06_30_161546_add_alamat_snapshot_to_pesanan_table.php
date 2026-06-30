<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Snapshot alamat pengiriman (agar perubahan address book tidak
            // mengubah data pesanan lama).
            $table->string('penerima', 120)->nullable()->after('alamat_id');
            $table->string('telepon', 25)->nullable()->after('penerima');
            $table->string('kota', 80)->nullable()->after('telepon');
            $table->text('alamat_lengkap')->nullable()->after('kota');
            $table->string('kode_pos', 10)->nullable()->after('alamat_lengkap');
            // alamat_id kini opsional (boleh merujuk address book atau null).
            $table->unsignedBigInteger('alamat_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['penerima', 'telepon', 'kota', 'alamat_lengkap', 'kode_pos']);
        });
    }
};
