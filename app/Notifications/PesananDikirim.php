<?php

namespace App\Notifications;

use App\Models\Pesanan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email saat pesanan dikirim, menyertakan nomor resi bila ada. */
class PesananDikirim extends Notification
{
    public function __construct(public Pesanan $pesanan) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->pesanan;

        $mail = (new MailMessage)
            ->subject('Pesanan '.$p->kode.' sedang dikirim — '.config('toko.nama'))
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Kabar baik! Pesanan Anda sudah diserahkan ke kurir '.strtoupper((string) $p->kurir).'.');

        if ($p->resi) {
            $mail->line('Nomor resi: **'.$p->resi.'**');
        }

        return $mail
            ->action('Lacak Pesanan', route('pesanan.show', $p->kode))
            ->line('Jangan lupa konfirmasi "Pesanan Diterima" setelah barang sampai.')
            ->salutation('Salam hangat, Tim '.config('toko.nama'));
    }
}
