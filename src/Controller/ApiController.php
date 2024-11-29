<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Prospect;
use App\Entity\User;
use App\Entity\UserCampaign;
use App\Form\ProspectApiType;
use App\Repository\ProspectRepository;
use App\Repository\UserCampaignRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

class ApiController extends AbstractController
{
    public function __construct(private readonly UserService $userService, private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/api/campaign/{campaign}/prospects', name: 'api_campaign_prospect', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getProspectsByCampaign(Campaign $campaign)
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$this->userService->isAbilitedToCampaign($user, $campaign)) {
            return $this->json(['message' => "Vous n'êtes pas autorisé"], 400);
        }

        /** @var ProspectRepository $prospectRepository */
        $prospectRepository = $this->entityManager->getRepository(Prospect::class);

        $prospects = $prospectRepository->findBy([
            'campaign' => $campaign,
        ]);

        $prospectsFiltered = [];

        /** @var Prospect $prospect */
        foreach ($prospects as $prospect) {
            if ($prospect->getRappel() === null && $prospect->getRendezvous() === null) {
                $prospectsFiltered[] = $prospect;
            }
        }

        return $this->success($prospectsFiltered, ['show_prospect']);
    }

    #[Route(path: '/api/prospect/{prospect}', name: 'api_prospect_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateProspect(Prospect $prospect, Request $request)
    {
        $form = $this->createForm(ProspectApiType::class, $prospect);
        $form->submit($request->getPayload()->all());

        if ($form->get('delete')->getData() === true) {
            $this->entityManager->remove($prospect);
            $this->entityManager->flush();

            return $this->json(['message' => 'success']);
        }

        if ($prospect->getRappel() !== null) {
            $prospect->setOwner($this->getUser());
        }

        if ($prospect->getRendezvous() !== null) {
            $prospect->setOwner($this->getUser());
        }

        $prospect->setUpdatedAt(new \DateTime());

        /** @var UserCampaignRepository $userCampaignRepository */
        $userCampaignRepository = $this->entityManager->getRepository(UserCampaign::class);

        /** @var UserCampaign $userCampaign */
        $userCampaign = $userCampaignRepository->findOneBy(['campaign' => $prospect->getCampaign(), 'user' => $this->getUser()]);

        /** @var ProspectRepository $prospectRepository */
        $prospectRepository = $this->entityManager->getRepository(Prospect::class);

        $prospects = $prospectRepository->findBy([
            'campaign' => $prospect->getCampaign(),
        ]);


        $findProspect = false;
        $idActual = null;

        /** @var Prospect $prospectEntity */
        foreach ($prospects as $prospectEntity) {
            if ($prospectEntity->getId() === $prospect->getId() && $prospectEntity->getOwner() === null) {
                $findProspect = true;
            } else if ($findProspect === true && $idActual === null) {
                $idActual = $prospectEntity->getId();
            }
        }

        $userCampaign->setIdActual($idActual);

        $this->entityManager->flush();

        return $this->json(['message' => 'success']);
    }

    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];

        // Fonction récursive pour organiser les erreurs dans une structure adaptée
        $extractErrors = function (FormInterface $form, array &$errorsArray) use (&$extractErrors) {
            // Ajouter les erreurs du formulaire lui-même si elles existent
            foreach ($form->getErrors() as $error) {
                $errorsArray['root'] = $errorsArray['root'] ?? [];
                $errorsArray['root'][] = $error->getMessage();
            }

            // Itérer sur les enfants (sous-formulaires ou champs)
            foreach ($form as $child) {
                $childErrors = [];
                //$childSpecialErrors = [];
                // S'il s'agit d'un sous-formulaire, récursive
                if ($child->count() > 0) {
                    $extractErrors($child, $childErrors);
                } else {
                    /** @var FormError $error */
                    // Pour un champ normal, collecter les erreurs
                    foreach ($child->getErrors() as $error) {
                        $childErrors[] = $error->getMessage();

                        /*if ($error instanceof FormError && null !== $error->getCause()) {
                            $childSpecialErrors[] = $error->getCause();
                        }*/
                    }
                }
                // Ajouter les erreurs du champ ou sous-formulaire s'il y en a
                if (!empty($childErrors)) {
                    /*if (!empty($childSpecialErrors)) {
                        $errorsArray[$child->getName()] = $childSpecialErrors;
                    } else {
                        $errorsArray[$child->getName()] = $childErrors;
                    }*/
                    $errorsArray[$child->getName()] = $childErrors;
                }
            }
        };

        // Appliquer la fonction récursive sur le formulaire
        $extractErrors($form, $errors);

        return $errors;
    }

    #[Route(path: '/api/usercampaign/{campaign}', name: 'api_user_campaign_value', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getIdActualInUserCampaign(Campaign $campaign)
    {
        /** @var UserCampaignRepository $userCampaignRepository */
        $userCampaignRepository = $this->entityManager->getRepository(UserCampaign::class);

        /** @var UserCampaign $userCampaign */
        $userCampaign = $userCampaignRepository->findOneBy(['campaign' => $campaign, 'user' => $this->getUser()]);

        if ($userCampaign === null) {
            return $this->json(['message' => 'user campaign not found'], 404);
        }

        return $this->success($userCampaign, ['show_user_campaign']);
    }

    protected function success(mixed $data, array $groups = []): JsonResponse
    {
        return $this->json($data, 200, [], $this->getContextWithGroups($groups));
    }


    protected function getContextWithGroups(array $groups = []): array
    {
        return (new ObjectNormalizerContextBuilder())
            ->withGroups($groups)
            ->toArray();
    }


}
