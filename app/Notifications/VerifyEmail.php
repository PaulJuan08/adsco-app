<?php
// app/Notifications/VerifyEmail.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        
        return (new MailMessage)
            ->subject('Verify Your Email Address - ADSCO LMS')
            ->greeting('Hello ' . ($notifiable->f_name ?? 'User') . '!')
            ->line('Thank you for registering with ADSCO Learning Management System.')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you did not create an account, no further action is required.')
            ->line('This verification link will expire in 60 minutes.')
            ->salutation('Best regards,<br>ADSCO LMS Team');
    }

    protected function verificationUrl($notifiable)
    {
        // Encrypt the user ID
        $encryptedId = Crypt::encrypt($notifiable->getKey());
        
        // Generate a signed URL with encrypted ID
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'encryptedId' => $encryptedId,
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}