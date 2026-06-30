<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('diskon', 12, 2)->default(0)->after('subtotal');
            $table->string('kode_voucher', 30)->nullable()->after('diskon');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['diskon', 'kode_voucher']);
        });
    }
};
