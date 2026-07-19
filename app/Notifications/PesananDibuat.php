<?php

namespace App\Notifications;

use App\Models\Pesanan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email konfirmasi setelah checkout: instruksi bayar (atau konfirmasi COD). */
class PesananDibuat extends Notification
{
    public function __construct(public Pesanan $pesanan) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->pesanan;
        $total = 'Rp'.number_format($p->total, 0, ',', '.');

        if ($p->isCod()) {
            return (new MailMessage)
                ->subject('Pesanan '.$p->kode.' dikonfirmasi (COD) — '.config('toko.nama'))
                ->greeting('Halo, '.$notifiable->name.'!')
                ->line('Pesanan Anda dikonfirmasi dengan metode Bayar di Tempat (COD).')
                ->line('Total yang harus disiapkan tunai: **'.$total.'**')
                ->action('Lihat Pesanan', route('pesanan.show', $p->kode))
                ->salutation('Salam hangat, Tim '.config('toko.nama'));
        }

        $batas = $p->created_at->addHours((int) config('toko.pesanan_expire_jam', 24));

        return (new MailMessage)
            ->subject('Pesanan '.$p->kode.' menunggu pembayaran — '.config('toko.nama'))
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Terima kasih, pesanan Anda berhasil dibuat.')
            ->line('Total: **'.$total.'** via '.$p->labelMetode().'.')
            ->line('Selesaikan pembayaran sebelum **'.$batas->translatedFormat('d M Y, H:i').'** agar pesanan tidak dibatalkan otomatis.')
            ->action('Bayar Sekarang', route('pesanan.show', $p->kode))
            ->salutation('Salam hangat, Tim '.config('toko.nama'));
    }
}
