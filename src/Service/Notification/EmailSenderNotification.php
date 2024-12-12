<?php

namespace App\Service\Notification;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailSenderNotification implements SenderInterface
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function send(string $to, string $template): bool
    {
        $email = (new TemplatedEmail())
            ->from($_ENV["MAIL_FROM"])
            ->to($to)
            ->subject('Rappel rendez-vous')
            ->htmlTemplate('email/template.html.twig')
            ->context([
                'template' => $template
            ])
        ;

        $this->mailer->send($email);

        return true;
    }
}
