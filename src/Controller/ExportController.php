<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Prospect;
use App\Service\Export\ProspectExporter;
use App\Service\Export\RendezVousExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/export')]
#[IsGranted('ROLE_USER')]
class ExportController extends AbstractController
{
    #[Route('/{entity}', name: 'app_export', methods: ['GET'])]
    public function export(string $entity, Request $request, EntityManagerInterface $entityManager, ProspectExporter $prospectExporter, RendezVousExporter $rendezVousExporter): BinaryFileResponse
    {
        $campaignId = $request->query->get('campaign');

        /** @var \App\Repository\CampaignRepository $campaignRepository **/
        $campaignRepository = $entityManager->getRepository(Campaign::class);

        $campaign = $campaignRepository->find($campaignId);


        if ($entity === 'prospect') {
            $prospectExporter->process($campaign);

            $dataCSV = $prospectExporter->export();
        }

        if ($entity === 'rendezvous') {
            $rendezVousExporter->process($campaign);

            $dataCSV = $rendezVousExporter->export();
        }

        if (isset($dataCSV)) {
            $fileName = md5(time()) . ".csv";

            file_put_contents(__DIR__ . "/../../public/export/" . $fileName, $dataCSV);

            return new BinaryFileResponse(__DIR__ . "/../../public/export/" . $fileName);

        }

        throw new NotFoundHttpException();
    }
}
