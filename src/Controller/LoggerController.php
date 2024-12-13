<?php

namespace App\Controller;

use App\Entity\Logger;
use App\Form\LoggerType;
use App\Repository\LoggerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/logger')]
#[IsGranted('ROLE_ADMIN')]
final class LoggerController extends AbstractController
{
    #[Route(name: 'app_logger_index', methods: ['GET'])]
    public function index(LoggerRepository $loggerRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $loggers = $loggerRepository->findBy([], ['id' => 'DESC']);

        $pagination = $paginator->paginate(
            $loggers,
            $request->query->getInt('page', 1),
            50
        );

        return $this->render('logger/index.html.twig', [
            'loggers' => $pagination,
        ]);
    }

    #[Route('/{id}', name: 'app_logger_show', methods: ['GET'])]
    public function show(Logger $logger): Response
    {
        return $this->render('logger/show.html.twig', [
            'logger' => $logger,
        ]);
    }
}
