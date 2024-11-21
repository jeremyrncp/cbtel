<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProspectionController extends AbstractController
{
    #[Route('/prospection', name: 'app_prospection')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user instanceof InMemoryUser) {
            return $this->redirectToRoute("app_prospect_index");
        }

        return $this->render('prospection/index.html.twig', [
            'UserCampaigns' => $user->getUserCampaigns(),
            "iframe" => $_ENV["IFRAME_PROSPECTION"]
        ]);
    }
}
