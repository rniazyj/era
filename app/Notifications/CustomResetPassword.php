<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Permintaan Reset Password — Galeri Magang')
            ->view('emails.auth.reset-password', [
                'name' => $notifiable->name,
                'url' => $url,
            ]);
    }
}