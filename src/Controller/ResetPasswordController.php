<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use App\Service\TokenPasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ResetPasswordController extends AbstractController
{
    public function __construct(private readonly TokenPasswordService $tokenPasswordService)
    {
    }

    #[Route('/reset_password/{token}', name: 'app_reset_password')]
    public function index(string $token, Request $request): Response
    {
        $message = null;

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->tokenPasswordService->resetPassword($token, $form->get('password')->getData());

            if ($result) {
                $message = "Mot de passe défini, veuillez vous connecter. <a href='/login'>Se connecter</a>";
            } else {
                $message = "Une erreur est survenue, merci de contacter le <a href='mailto:cedric.bert@cbtel.fr'>support</a>";
            }
        }

        return $this->render('reset_password/index.html.twig', [
            'form' => $form->createView(),
            'messsage' => $message
        ]);
    }
}
