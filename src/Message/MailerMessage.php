<?php

namespace App\Message;

use Symfony\Component\Mime\Email;

final class MailerMessage
{
    public function __construct(
        private Email $email
    )
    {
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
