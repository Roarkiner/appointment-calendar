<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Slot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getSlot'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getSlot'])]
    #[Type("DateTime<'Y-m-d H:i'>")]
    #[Constraints\NotNull(message: "Start date cannot be null.")]
    #[Constraints\GreaterThan('now', message: "Start date must be in the future.")]
    #[Constraints\Callback([Slot::class, 'validateMinutes'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getSlot'])]
    #[Type("DateTime<'Y-m-d H:i'>")]
    #[Constraints\NotNull(message: "End date cannot be null.")]
    #[Constraints\Expression(
        "value >= this.getStartDate()",
        message: "End date cannot be before start date."
    )]
    #[Constraints\Callback([Slot::class, 'validateMinutes'])]
    private ?\DateTimeInterface $endDate = null;

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
