<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Entity\Prospect;
use App\Form\ImportProspectType;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImportProspectController extends AbstractController
{
    #[Route('/import/prospect', name: 'app_import_prospect')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $count = 0;

        $importProspectForm = $this->createForm(ImportProspectType::class);
        $importProspectForm->handleRequest($request);

        if ($importProspectForm->isSubmitted() && $importProspectForm->isValid()) {
            /** @var UploadedFile $file */
            $file = $importProspectForm['file']->getData();

            /** @var Campaign $campaign */
            $campaign = $importProspectForm['campaign']->getData();

            switch ($file->guessExtension()) {
                case 'xlsx':
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    break;
                case 'xls':
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                    break;
            }

            $spreadsheet = $reader->load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            $arrayWorksheet = $worksheet->toArray();

            foreach ($arrayWorksheet as $data) {
                if ($data[1] !== "Activité" && $data[4] !== null  && $data[1] !== "1") {
                    $entity = new Prospect();
                    $entity->setCompany($data[0])
                        ->setActivity($data[1])
                        ->setAddress($data[2])
                        ->setPostalCode($data[3])
                        ->setCity($data[4])
                        ->setPhone($data[5])
                        ->setMobile($data[6])
                        ->setEmail($data[7])
                        ->setCampaign($campaign);


                    if (isset($data[8])) {
                        $entity
                            ->setCommentary($data[8]);
                    }
                    
                    $em->persist($entity);
                    $count ++;
                }
            }

            $em->flush();
        }

        return $this->render('import_prospect/index.html.twig', [
            'form' => $importProspectForm->createView(),
            'count' => $count
        ]);
    }
}
