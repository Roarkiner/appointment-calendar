<?php

namespace App\Entity;

use App\Repository\ServiceTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ServiceTypeRepository::class)]
class ServiceType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getServiceType', 'getAppointment', 'getUser'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getServiceType', 'getAppointment'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['getServiceType', 'getAppointment'])]
    private ?\DateInterval $duration = null;

    #[ORM\Column]
    private ?bool $status = null;

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

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(\DateInterval $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }
}
