<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    /**
     * @var Collection<int, DefaultTemplate>
     */
    #[ORM\OneToMany(targetEntity: DefaultTemplate::class, mappedBy: 'template')]
    private Collection $defaultTemplates;

    public function __construct()
    {
        $this->defaultTemplates = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, DefaultTemplate>
     */
    public function getDefaultTemplates(): Collection
    {
        return $this->defaultTemplates;
    }

    public function addDefaultTemplate(DefaultTemplate $defaultTemplate): static
    {
        if (!$this->defaultTemplates->contains($defaultTemplate)) {
            $this->defaultTemplates->add($defaultTemplate);
            $defaultTemplate->setTemplate($this);
        }

        return $this;
    }

    public function removeDefaultTemplate(DefaultTemplate $defaultTemplate): static
    {
        if ($this->defaultTemplates->removeElement($defaultTemplate)) {
            // set the owning side to null (unless already changed)
            if ($defaultTemplate->getTemplate() === $this) {
                $defaultTemplate->setTemplate(null);
            }
        }

        return $this;
    }
}
