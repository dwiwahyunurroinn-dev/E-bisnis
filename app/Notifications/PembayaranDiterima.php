<?php

namespace App\Notifications;

use App\Models\Pesanan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email saat pesanan lunas / dikonfirmasi — pesanan mulai diproses. */
class PembayaranDiterima extends Notification
{
    public function __construct(public Pesanan $pesanan) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->pesanan;

        return (new MailMessage)
            ->subject(($p->isCod() ? 'Pesanan '.$p->kode.' dikonfirmasi' : 'Pembayaran '.$p->kode.' diterima').' — '.config('toko.nama'))
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line($p->isCod()
                ? 'Pesanan COD Anda dikonfirmasi dan mulai kami proses.'
                : 'Pembayaran Anda sebesar **Rp'.number_format($p->total, 0, ',', '.').'** telah kami terima.')
            ->line('Pesanan sedang kami siapkan dan akan segera dikirim.')
            ->action('Lacak Pesanan', route('pesanan.show', $p->kode))
            ->salutation('Salam hangat, Tim '.config('toko.nama'));
    }
}
