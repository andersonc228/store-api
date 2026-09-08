<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Email;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailSender
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function send(string $to, string $subject, string $text, string $from): void
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->text($text);

        $this->mailer->send($email);
    }
}
