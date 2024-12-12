<?php

namespace App\Service\Notification;

interface SenderInterface
{
    public function send(string $to, string $template): bool;
}
