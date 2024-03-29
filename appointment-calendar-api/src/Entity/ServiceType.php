<?php

namespace App\Entity;

use App\Repository\ServiceTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceTypeRepository::class)]
class ServiceType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column]
    private ?\DateInterval $Duration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->Duration;
    }

    public function setDuration(\DateInterval $Duration): static
    {
        $this->Duration = $Duration;

        return $this;
    }
}
