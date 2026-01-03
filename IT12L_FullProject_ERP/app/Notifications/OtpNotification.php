<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Your OTP Verification Code')
                    ->greeting('Hello!')
                    ->line('Your one-time password (OTP) is:')
                    ->line(new \Illuminate\Support\HtmlString('<h2 style="text-align: center; font-size: 32px; color: #d9534f; background: #fdf2f2; padding: 10px; border-radius: 5px;">' . $this->otp . '</h2>'))
                    ->line('This code will expire in 10 minutes.')
                    ->line('If you did not request this code, please ignore this email.')
                    ->salutation('Best regards, BBQ Lagao Team');
    }
}
