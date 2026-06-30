<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Trigger MySQL: saat status pesanan berubah menjadi 'lunas',
     * kurangi stok produk jadi DAN stok bahan baku (real-time inventory).
     * Hanya dijalankan pada koneksi MySQL/MariaDB.
     */
    public function up(): void
    {
        if (! $this->isMySql()) {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_kurangi_stok_produk');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_kurangi_stok_produk
            AFTER UPDATE ON pesanan
            FOR EACH ROW
            BEGIN
                IF NEW.status = 'lunas' AND OLD.status <> 'lunas' THEN
                    UPDATE produk p
                    JOIN detail_pesanan dp ON dp.produk_id = p.id
                    SET p.stok = p.stok - dp.jumlah
                    WHERE dp.pesanan_id = NEW.id;

                    UPDATE bahan_baku b
                    JOIN produk_bahan_baku pbb ON pbb.bahan_baku_id = b.id
                    JOIN detail_pesanan dp      ON dp.produk_id = pbb.produk_id
                    SET b.stok = b.stok - (pbb.jumlah * dp.jumlah)
                    WHERE dp.pesanan_id = NEW.id;
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        if ($this->isMySql()) {
            DB::unprepared('DROP TRIGGER IF EXISTS trg_kurangi_stok_produk');
        }
    }

    private function isMySql(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }
};
