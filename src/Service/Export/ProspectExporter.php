<?php

namespace App\Service\Export;

use App\Entity\Campaign;
use App\Entity\Prospect;
use App\Repository\ProspectRepository;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProspectExporter implements ExporterInterface
{
    private string $dataEncoded;

    public function __construct(
        private readonly ProspectRepository $prospectRepository
    ) {
    }

    public function process(Campaign $campaign = null)
    {
        if (is_null($campaign)) {
            $prospects = $this->prospectRepository->findAll();
        } else {
            $prospects = $this->prospectRepository->findBy([
                'campaign' => $campaign,
            ]);
        }

        $prospectEncoded = $this->getEncodedData($prospects);

        array_unshift($prospectEncoded , $this->getHeaders());

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        $this->dataEncoded = $serializer->encode($prospectEncoded, 'csv');
    }

    public function export(): string
    {
        return $this->dataEncoded;
    }

    private function getEncodedData(array $prospects): array
    {
        $encoded = [];

        /** @var Prospect $prospect */
        foreach ($prospects as $prospect) {
            $encoded[] = [
                $prospect->getCompany(),
                $prospect->getActivity(),
                $prospect->getAddress(),
                $prospect->getPostalCode(),
                $prospect->getCity(),
                $prospect->getPhone(),
                $prospect->getMobile(),
                $prospect->getEmail(),
                $prospect->getCommentary(),
                $prospect->getRappel()?->format("Y-m-d H:i:s"),
                $prospect->getRendezvous()?->format("Y-m-d H:i:s"),
                $prospect->getCampaign()->getName()
            ];
        }

        return $encoded;
    }

    private function getHeaders(): array
    {
        return [
            'compagni',
            'Activité',
            'Adresse',
            'C.P.',
            'Ville',
            'phone',
            'Mobile',
            'Email',
            'Commentary',
            'Rappel',
            'RDV',
            'Campaign'
        ];
    }
}
