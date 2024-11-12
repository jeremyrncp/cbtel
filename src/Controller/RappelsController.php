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

#[Route('/rappels')]
#[IsGranted('ROLE_USER')]
final class RappelsController extends AbstractController
{
    #[Route(name: 'app_rappels_index', methods: ['GET', 'POST'])]
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

        $prospectsWithRappel = [];

        /** @var Prospect $prospect */
        foreach ($prospects as $prospect) {
            if (null !== $prospect && $prospect->getRappel() > new \DateTime() && ($this->isGranted("ROLE_ADMIN") OR $this->getUser() === $prospect->getOwner())) {
                $prospectsWithRappel[] = $prospect;
            }
        }

        $pagination = $paginator->paginate(
            $prospectsWithRappel,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('rappels/index.html.twig', [
            'prospects' => $pagination,
            'filterForm' => $filterProspectForm->createView(),
            'campaignId' => $campaignId,
        ]);
    }
}
