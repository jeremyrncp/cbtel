<?php

namespace App\Service\Notification;

use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public const TYPE_SMS = "sms";
    public const TYPE_EMAIL = "email";

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
}
