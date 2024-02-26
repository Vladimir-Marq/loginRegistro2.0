<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginTokenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    /**
     * Crea una nueva instancia de mensaje.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Construye el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.loginToken')
                    ->with([
                        'token' => $this->token,
                    ]);
    }
}