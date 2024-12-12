<?php

namespace App\Controller;

use App\Entity\DefaultTemplate;
use App\Form\DefaultTemplateType;
use App\Repository\DefaultTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/templatedefault')]
#[IsGranted('ROLE_ADMIN')]
final class DefaultTemplateController extends AbstractController
{
    #[Route(name: 'app_default_template_index', methods: ['GET'])]
    public function index(DefaultTemplateRepository $defaultTemplateRepository): Response
    {
        return $this->render('default_template/index.html.twig', [
            'default_templates' => $defaultTemplateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_default_template_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $defaultTemplate = new DefaultTemplate();
        $form = $this->createForm(DefaultTemplateType::class, $defaultTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var DefaultTemplateRepository $defaultTemplateRepository */
            $defaultTemplateRepository = $entityManager->getRepository(DefaultTemplate::class);

            $defaultTemplateEntity = $defaultTemplateRepository->findOneBy(['type' => $defaultTemplate->getType()]);

            if ($defaultTemplateEntity === null) {
                $entityManager->persist($defaultTemplate);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_default_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('default_template/new.html.twig', [
            'default_template' => $defaultTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_default_template_show', methods: ['GET'])]
    public function show(DefaultTemplate $defaultTemplate): Response
    {
        return $this->render('default_template/show.html.twig', [
            'default_template' => $defaultTemplate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_default_template_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DefaultTemplate $defaultTemplate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DefaultTemplateType::class, $defaultTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_default_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('default_template/edit.html.twig', [
            'default_template' => $defaultTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_default_template_delete', methods: ['POST'])]
    public function delete(Request $request, DefaultTemplate $defaultTemplate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$defaultTemplate->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($defaultTemplate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_default_template_index', [], Response::HTTP_SEE_OTHER);
    }
}
