<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CredenciaisMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $senha;
    public $loginUrl;

    public function __construct($user, $senha, $loginUrl)
    {
        $this->user = $user;
        $this->senha = $senha;
        $this->loginUrl = $loginUrl;
    }

    public function build()
    {
        return $this->subject('Suas credenciais de acesso - AlphaCode')
            ->view('emails.credenciais');
    }
}