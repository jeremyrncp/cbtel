<?php

namespace App\Entity;

use App\Repository\ProspectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProspectRepository::class)]
class Prospect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_prospect'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prospects')]
    private ?Campaign $campaign = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?string $activity = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?string $mobile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdat = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?string $commentary = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?\DateTimeInterface $rappel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['show_prospect'])]
    private ?\DateTimeInterface $rendezvous = null;

    #[ORM\ManyToOne(inversedBy: 'prospects')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getActivity(): ?string
    {
        return $this->activity;
    }

    public function setActivity(?string $activity): static
    {
        $this->activity = $activity;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedat(): ?\DateTimeInterface
    {
        return $this->createdat;
    }

    public function setCreatedat(?\DateTimeInterface $createdat): static
    {
        $this->createdat = $createdat;

        return $this;
    }

    public function getCommentary(): ?string
    {
        return $this->commentary;
    }

    public function setCommentary(?string $commentary): static
    {
        $this->commentary = $commentary;

        return $this;
    }

    public function getRappel(): ?\DateTimeInterface
    {
        return $this->rappel;
    }

    public function setRappel(?\DateTimeInterface $rappel): static
    {
        $this->rappel = $rappel;

        return $this;
    }

    public function getRendezvous(): ?\DateTimeInterface
    {
        return $this->rendezvous;
    }

    public function setRendezvous(?\DateTimeInterface $rendezvous): static
    {
        $this->rendezvous = $rendezvous;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function __toString(): string
    {
        return $this->company;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
