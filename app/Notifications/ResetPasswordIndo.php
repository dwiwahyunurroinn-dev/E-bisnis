<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email reset kata sandi berbahasa Indonesia. */
class ResetPasswordIndo extends Notification
{
    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', ['token' => $this->token, 'email' => $notifiable->email]);

        return (new MailMessage)
            ->subject('Reset Kata Sandi — '.config('toko.nama'))
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Kami menerima permintaan reset kata sandi untuk akun Anda.')
            ->action('Reset Kata Sandi', $url)
            ->line('Tautan ini berlaku 60 menit. Abaikan email ini jika Anda tidak meminta reset.')
            ->salutation('Salam hangat, Tim '.config('toko.nama'));
    }
}
