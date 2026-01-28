<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserApprovedMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $user;
    
    public function __construct($user)
    {
        $this->user = $user;
    }
    
    public function build()
    {
        return $this->subject('Account Approved - ADSCO LMS')
                    ->view('emails.user-approved')
                    ->with(['user' => $this->user]);
    }
}