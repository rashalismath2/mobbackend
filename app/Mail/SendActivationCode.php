<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendActivationCode extends Mailable
{
    use Queueable, SerializesModels;

    public $activationCode;
    public $email;

    public function __construct($email,$code)
    {
        $this->activationCode=$code;
        $this->email=$email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("yobizsol@gmail.com")
            ->subject("MASTER - Verify email")
            ->markdown('Emails.SendActivationCode', [
                'activationCode' => $this->activationCode,
            ]);    
       
    }
}
