<?php

namespace App\Command;

use App\Entity\Prospect;
use App\Repository\ProspectRepository;
use App\Service\LoggerService;
use App\Service\Notification\NotificationService;
use App\Service\Notification\SenderNotificationFactory;
use App\Service\Notification\TemplateNotiifcationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:notification-rdv',
    description: 'Notification SMS rendez-vous',
)]
class NotificationRdvCommand extends Command
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly LoggerService $loggerService,
        private readonly SenderNotificationFactory $senderNotificationFactory,
        private readonly ProspectRepository $prospectRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TemplateNotiifcationService $templateNotiifcationService
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $template = $this->notificationService->getDefaultTemplate(NotificationService::TYPE_SMS);
        $prospects = $this->prospectRepository->getWithoutNotificationSMS((new \DateTime())->modify("+24 hours"));

        /** @var Prospect $prospect */
        foreach ($prospects as $prospect) {
            if ($template->getType() === NotificationService::TYPE_SMS && ($prospect->getMobile() === null OR $prospect->getMobile() === "")) {
                $this->loggerService->saveLog("Prospect", "Le mobile du prospect " . $prospect->getId() . " doit être définit");
            } else {
                $templateRendered = $this->templateNotiifcationService->render($template, $prospect);

                if ($template->getType() === NotificationService::TYPE_SMS) {
                    $to = $prospect->getMobile();
                }

                $sender = $this->senderNotificationFactory->create($template->getType());

                if (strlen($to) >= 10) {
                    if ($sender->send($to, $templateRendered)) {
                        $this->loggerService->saveLog("Prospect", "Notification sms transmise au prospect " . $prospect->getId());

                        if ($template->getType() === NotificationService::TYPE_SMS) {
                            $prospect->setSmsNotification(new \DateTime());
                        }
                    } else {
                        $this->loggerService->saveLog("Prospect", "Une erreur inconnue est survenue lors de l'envoie de la notification sms au prospect " . $prospect->getId());
                    }
                } else {
                    $this->loggerService->saveLog("Prospect", "Le numéro d'envoie du mobile n'est pas valide pour le prospect " . $prospect->getId());
                }
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
