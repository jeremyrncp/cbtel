<?php

namespace App\Controller;

use App\Entity\Prospect;
use App\Entity\Template;
use App\Service\Notification\NotificationService;
use App\Service\Notification\SenderNotificationFactory;
use App\Service\Notification\TemplateNotiifcationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SendTemplateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TemplateNotiifcationService $templateNotiifcationService,
        private readonly SenderNotificationFactory $senderNotificationFactory
    )
    {
    }

    #[Route('/send/selecttemplate/{prospect}/{type}', name: 'app_send_select_template')]
    #[IsGranted('ROLE_USER')]
    public function select(Prospect $prospect, string $type): Response
    {
        $errorMessage = null;

        if ($type !== NotificationService::TYPE_SMS && $type !== NotificationService::TYPE_EMAIL) {
            $errorMessage = "Vous devez choisir une template email ou sms";
        }

        $templateRepository = $this->entityManager->getRepository(Template::class);

        $templates = $templateRepository->findBy([
            'type' => $type
        ]);

        if (count($templates) === 0) {
            $errorMessage = "Aucune template définit pour le type " . $type . ", veuillez contacter un administrateur pour en définir";
        }

        return $this->render('send_template/index.html.twig', [
            'prospect' => $prospect,
            "templates" => $templates,
            'errorMessage' => $errorMessage
        ]);
    }

    #[Route('/send/template/{template}/{prospect}', name: 'app_send_template')]
    #[IsGranted('ROLE_USER')]
    public function send(Template $template, Prospect $prospect): Response
    {
        $errorMessage = null;
        $successMessage = null;

        //verify to
        if ($template->getType() === NotificationService::TYPE_EMAIL && ($prospect->getEmail() === null OR $prospect->getEmail() === "")) {
            $errorMessage = "L'email du prospect doit être définit";
        }

        if ($template->getType() === NotificationService::TYPE_SMS && ($prospect->getMobile() === null OR $prospect->getMobile() === "")) {
            $errorMessage = "Le mobile du prospect doit être définit";
        }

        if ($errorMessage === null) {
            $templateRendered = $this->templateNotiifcationService->render($template, $prospect);

            try {
                if ($template->getType() === NotificationService::TYPE_EMAIL) {
                    $to = $prospect->getEmail();
                }

                if ($template->getType() === NotificationService::TYPE_SMS) {
                    $to = $prospect->getMobile();
                }

                $sender = $this->senderNotificationFactory->create($template->getType());

                if ($sender->send($to, $templateRendered)) {
                    $successMessage = "Notification envoyée avec succès, veuillez fermer la page.";

                    if ($template->getType() === NotificationService::TYPE_EMAIL) {
                        $prospect->setEmailNotification(new \DateTime());
                    }

                    if ($template->getType() === NotificationService::TYPE_SMS) {
                        $prospect->setSmsNotification(new \DateTime());
                    }

                    $this->entityManager->flush();
                } else {
                    $errorMessage = "Une erreur inconnue est survenue lors de l'envoie de la notification";
                }
            } catch (\Exception $exception) {
                $errorMessage = "Une erreur est survenue lors de l'envoie de la notification (" . $exception->getMessage() . ")";
            }
        }


        return $this->render('send_template/send.html.twig', [
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage
        ]);
    }
}
