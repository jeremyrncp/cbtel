<?php

namespace App\Service\Notification;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SenderNotificationFactory
{
    public function __construct(
        private readonly SmsSenderNotification $smsSenderNotification,
        private readonly EmailSenderNotification $emailSenderNotification
    )
    {
    }

    public function create(string $type): SenderInterface
    {
        switch ($type) {
            case NotificationService::TYPE_SMS:
                return $this->smsSenderNotification;
            case NotificationService::TYPE_EMAIL:
                return $this->emailSenderNotification;
            default:
                throw new UnprocessableEntityHttpException();
        }
    }
}
