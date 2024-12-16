<?php

namespace App\Controller;

use App\Entity\Prospect;
use App\Form\FilterProspectType;
use App\Form\ProspectType;
use App\Repository\ProspectRepository;
use App\Service\Notification\SendEmailNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/prospect')]
#[IsGranted('ROLE_USER')]
final class ProspectController extends AbstractController
{
    public function __construct(
        private readonly SendEmailNotificationService $sendEmailNotificationService
    )
    {
    }

    #[Route(name: 'app_prospect_index', methods: ['GET', 'POST'])]
    public function index(ProspectRepository $prospectRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $campaignId = null;

        $filterProspectForm = $this->createForm(FilterProspectType::class);
        $filterProspectForm->handleRequest($request);

        if ($filterProspectForm->isSubmitted() && $filterProspectForm->isValid()) {
            $prospects = $prospectRepository->findBy([
                'campaign' => $filterProspectForm['campaign']->getData(),
            ]);
            $campaignId = $filterProspectForm['campaign']->getData()->getId();
        } else {
            $prospects = $prospectRepository->findAll();
        }

        $prospectFiltered = [];

        /** @var Prospect $prospect */
        foreach ($prospects as $prospect) {
            if ($prospect->getRendezvous() === null) {
                $prospectFiltered[] = $prospect;
            }
        }

        $pagination = $paginator->paginate(
            $prospectFiltered,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('prospect/index.html.twig', [
            'prospects' => $pagination,
            'filterForm' => $filterProspectForm->createView(),
            'campaignId' => $campaignId,
        ]);
    }

    #[Route('/new', name: 'app_prospect_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prospect = new Prospect();
        $form = $this->createForm(ProspectType::class, $prospect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prospect);
            $entityManager->flush();

            return $this->redirectToRoute('app_prospect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prospect/new.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prospect_show', methods: ['GET'])]
    public function show(Prospect $prospect): Response
    {
        return $this->render('prospect/show.html.twig', [
            'prospect' => $prospect,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prospect_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prospect $prospect, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProspectType::class, $prospect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($prospect->getRappel() !== null) {
                $prospect->setOwner($this->getUser());
            }

            if ($prospect->getRendezvous() !== null) {
                $prospect->setOwner($this->getUser());

                if ($prospect->getEmailNotification() === null) {
                    $this->sendEmailNotificationService->send($prospect);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_prospect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prospect/edit.html.twig', [
            'prospect' => $prospect,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prospect_delete', methods: ['POST'])]
    public function delete(Request $request, Prospect $prospect, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prospect->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($prospect);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prospect_index', [], Response::HTTP_SEE_OTHER);
    }
}
