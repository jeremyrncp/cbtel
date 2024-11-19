<?php

namespace App\Controller;

use App\Entity\UserCampaign;
use App\Form\UserCampaignType;
use App\Repository\UserCampaignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/usercampaign')]
#[IsGranted('ROLE_ADMIN')]
final class UserCampaignController extends AbstractController
{
    #[Route(name: 'app_user_campaign_index', methods: ['GET'])]
    public function index(UserCampaignRepository $userCampaignRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $userCampaignRepository->findAll(),
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('user_campaign/index.html.twig', [
            'user_campaigns' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_user_campaign_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userCampaign = new UserCampaign();
        $form = $this->createForm(UserCampaignType::class, $userCampaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userCampaign);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_campaign/new.html.twig', [
            'user_campaign' => $userCampaign,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_campaign_show', methods: ['GET'])]
    public function show(UserCampaign $userCampaign): Response
    {
        return $this->render('user_campaign/show.html.twig', [
            'user_campaign' => $userCampaign,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_campaign_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserCampaign $userCampaign, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserCampaignType::class, $userCampaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_campaign/edit.html.twig', [
            'user_campaign' => $userCampaign,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_campaign_delete', methods: ['POST'])]
    public function delete(Request $request, UserCampaign $userCampaign, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userCampaign->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userCampaign);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_campaign_index', [], Response::HTTP_SEE_OTHER);
    }
}
