<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getAppointment'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getAppointment'])]
    #[Type("DateTime<'Y-m-d H:i'>")]
    #[Constraints\NotNull(message: "Start date cannot be null.")]
    #[Constraints\GreaterThan('now', message: "Start date must be in the future.")]
    #[Constraints\Callback([Appointment::class, 'validateMinutes'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getAppointment'])]
    #[Type("DateTime<'Y-m-d H:i'>")]
    #[Constraints\NotNull(message: "End date cannot be null.")]
    #[Constraints\Expression(
        "value >= this.getStartDate()",
        message: "End date cannot be before start date."
    )]
    #[Constraints\Callback([Slot::class, 'validateMinutes'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['getAppointment'])]
    private ?ServiceType $serviceType = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['getAppointment'])]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }
    
    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }
    
    public function getServiceType(): ?ServiceType
    {
        return $this->serviceType;
    }
    
    public function setServiceType(?ServiceType $serviceType): static
    {
        $this->serviceType = $serviceType;

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

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public static function validateMinutes($object, $context)
    {
        if ($object === null) {
            return;
        }

        if (!in_array($object->format('i'), ['00', '15', '30', '45'])) {
            $context->buildViolation('The minute must be 0, 15, 30, or 45.')
                    ->addViolation();
        }
    }
}
