<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Notification;

class TestMail extends Command
{
    protected $signature = 'mail:test {email}';
    protected $description = 'Send a test OTP email to verify SMTP settings';

    public function handle()
    {
        $email = $this->argument('email');
        $otp = '123456';

        $this->info("Sending test OTP [{$otp}] to {$email}...");

        try {
            Notification::route('mail', $email)->notify(new OtpNotification($otp));
            $this->info('Test email sent successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }
    }
}
