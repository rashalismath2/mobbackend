<?php

namespace App\Listeners;

use App\Events\EmailActivationRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Mail;

use App\Mail\SendActivationCode; 

class SendEmailVerificationCode
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EmailActivationRequest  $event
     * @return void
     */
    public function handle(EmailActivationRequest $event)
    {
        error_log("email");
        error_log($event->email);
        Mail::to($event->email)->send(new SendActivationCode($event->email,$event->code));
    }
}
