<?php

namespace App\Entity;

use App\Repository\UserCampaignRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserCampaignRepository::class)]
class UserCampaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_user_campaign'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userCampaigns')]
    private ?Campaign $campaign = null;

    #[ORM\ManyToOne(inversedBy: 'userCampaigns')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['show_user_campaign'])]
    private ?int $idActual = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(?Campaign $campaign): static
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIdActual(): ?int
    {
        return $this->idActual;
    }

    public function setIdActual(?int $idActual): static
    {
        $this->idActual = $idActual;

        return $this;
    }
}
