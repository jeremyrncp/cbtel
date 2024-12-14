<?php

namespace App\Service\Notification;

use App\Entity\Prospect;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;

class SendEmailNotificationService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly LoggerService $loggerService,
        private readonly SenderNotificationFactory $senderNotificationFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly TemplateNotiifcationService $templateNotiifcationService
    )
    {
    }

    public function send(Prospect $prospect): void
    {
        $template = $this->notificationService->getDefaultTemplate(NotificationService::TYPE_EMAIL);

        if ($template->getType() === NotificationService::TYPE_EMAIL && ($prospect->getEmail() === null OR $prospect->getEmail() === "")) {
            $this->loggerService->saveLog("Prospect", "L'email du prospect " . $prospect->getId() . " doit être définit");
        } else {
            $templateRendered = $this->templateNotiifcationService->render($template, $prospect);

            if ($template->getType() === NotificationService::TYPE_EMAIL) {
                $to = $prospect->getEmail();
            }

            if (isset($to)) {
                $sender = $this->senderNotificationFactory->create($template->getType());
                if ($sender->send($to, $templateRendered)) {
                    $this->loggerService->saveLog("Prospect", "Notification email transmise au prospect " . $prospect->getId());

                    if ($template->getType() === NotificationService::TYPE_EMAIL) {
                        $prospect->setEmailNotification(new \DateTime());
                    }
                } else {
                    $this->loggerService->saveLog("Prospect", "Une erreur inconnue est survenue lors de l'envoie de la notification email au prospect " . $prospect->getId());
                }
            }
        }

        $this->entityManager->flush();
    }
}
