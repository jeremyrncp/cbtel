<?php

namespace App\Controller;

use App\Entity\Prospect;
use App\Form\FilterProspectType;
use App\Form\ProspectType;
use App\Repository\ProspectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rendezvous')]
#[IsGranted('ROLE_USER')]
final class RendezVousController extends AbstractController
{
    #[Route(name: 'app_rendezvous_index', methods: ['GET', 'POST'])]
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

        $prospectsWithRDV = [];

        /** @var Prospect $prospect */
        foreach ($prospects as $prospect) {
            if (null !== $prospect && $prospect->getRendezvous() > new \DateTime() && ($this->isGranted("ROLE_ADMIN") OR $this->getUser() === $prospect->getOwner())) {
                $prospectsWithRDV[] = $prospect;
            }
        }

        $pagination = $paginator->paginate(
            $prospectsWithRDV,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('rendez_vous/index.html.twig', [
            'prospects' => $pagination,
            'filterForm' => $filterProspectForm->createView(),
            'campaignId' => $campaignId,
        ]);
    }
}
