<?php

namespace App\Service\Notification;

use App\Entity\DefaultTemplate;
use App\Entity\Template;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class NotificationService
{
    public const TYPE_SMS = "sms";
    public const TYPE_EMAIL = "email";

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getDefaultTemplate(string $type): Template
    {
        if ($type !== self::TYPE_SMS AND $type !== self::TYPE_EMAIL) {
            throw new UnprocessableEntityHttpException();
        }

        $defaultTemplateRepository = $this->entityManager->getRepository(DefaultTemplate::class);

        /** @var DefaultTemplate $defaultTemplate */
        $defaultTemplate = $defaultTemplateRepository->findOneBy(['type' => $type]);

        if ($defaultTemplate === null) {
            throw new UnprocessableEntityHttpException();
        }

        return $defaultTemplate->getTemplate();
    }
}
