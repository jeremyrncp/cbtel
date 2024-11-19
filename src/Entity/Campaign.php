<?php

namespace App\Entity;

use App\Repository\CampaignRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampaignRepository::class)]
class Campaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @var Collection<int, Prospect>
     */
    #[ORM\OneToMany(targetEntity: Prospect::class, mappedBy: 'campaign')]
    private Collection $prospects;

    /**
     * @var Collection<int, UserCampaign>
     */
    #[ORM\OneToMany(targetEntity: UserCampaign::class, mappedBy: 'campaign')]
    private Collection $userCampaigns;

    public function __construct()
    {
        $this->prospects = new ArrayCollection();
        $this->userCampaigns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Prospect>
     */
    public function getProspects(): Collection
    {
        return $this->prospects;
    }

    public function addProspect(Prospect $prospect): static
    {
        if (!$this->prospects->contains($prospect)) {
            $this->prospects->add($prospect);
            $prospect->setCampaign($this);
        }

        return $this;
    }

    public function removeProspect(Prospect $prospect): static
    {
        if ($this->prospects->removeElement($prospect)) {
            // set the owning side to null (unless already changed)
            if ($prospect->getCampaign() === $this) {
                $prospect->setCampaign(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserCampaign>
     */
    public function getUserCampaigns(): Collection
    {
        return $this->userCampaigns;
    }

    public function addUserCampaign(UserCampaign $userCampaign): static
    {
        if (!$this->userCampaigns->contains($userCampaign)) {
            $this->userCampaigns->add($userCampaign);
            $userCampaign->setCampaign($this);
        }

        return $this;
    }

    public function removeUserCampaign(UserCampaign $userCampaign): static
    {
        if ($this->userCampaigns->removeElement($userCampaign)) {
            // set the owning side to null (unless already changed)
            if ($userCampaign->getCampaign() === $this) {
                $userCampaign->setCampaign(null);
            }
        }

        return $this;
    }
}
