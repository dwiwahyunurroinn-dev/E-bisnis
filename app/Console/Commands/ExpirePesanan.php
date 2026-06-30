<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use App\Services\PesananService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('pesanan:expire')]
#[Description('Batalkan otomatis pesanan pending yang melewati batas waktu pembayaran')]
class ExpirePesanan extends Command
{
    public function handle(PesananService $service): int
    {
        $jam = (int) config('toko.pesanan_expire_jam', 24);
        $batas = now()->subHours($jam);

        $pesanan = Pesanan::where('status', 'pending')
            ->where('created_at', '<', $batas)
            ->get();

        foreach ($pesanan as $p) {
            $service->batalkan($p);   // pending -> tidak ada stok yang perlu dikembalikan
            Notifikasi::kirim($p->user_id, 'Pesanan dibatalkan otomatis',
                'Pesanan '.$p->kode.' dibatalkan karena melewati batas waktu pembayaran ('.$jam.' jam).',
                route('pesanan.show', $p->kode), 'status');
            ActivityLog::catat('batal otomatis', 'Pesanan '.$p->kode, 'Lewat batas '.$jam.' jam');
        }

        $this->info("Dibatalkan: {$pesanan->count()} pesanan kedaluwarsa.");

        return self::SUCCESS;
    }
}
